<?php

namespace App\Services;

use App\Models\AlertRule;
use App\Models\AlertTrigger;
use App\Models\PriceData;
use Illuminate\Support\Collection;

/**
 * Evaluates active alert rules against the latest available data (per ADR-0002, this is
 * delayed/batch data — evaluation runs once daily via the alerts:evaluate scheduled
 * command, not on every price tick). See FRS Module 8.
 */
class AlertEvaluationService
{
    /** @return Collection<int, AlertTrigger> newly created triggers this run */
    public function evaluateAll(): Collection
    {
        $latestDate = PriceData::max('trade_date');
        if (! $latestDate) {
            return collect();
        }

        $rules = AlertRule::query()
            ->where('active', true)
            ->with(['watchlistItem.security.priceData', 'watchlistItem.security.shariahStatuses'])
            ->get();

        return $rules->map(fn (AlertRule $rule) => $this->evaluateRule($rule, $latestDate))->filter()->values();
    }

    private function evaluateRule(AlertRule $rule, string $latestDate): ?AlertTrigger
    {
        $security = $rule->watchlistItem->security;
        $prices = $security->priceData->sortBy('trade_date')->values();

        return match ($rule->type) {
            'price_change_pct' => $this->checkPriceChange($rule, $prices, $latestDate),
            'volume_spike' => $this->checkVolumeSpike($rule, $prices, $latestDate),
            'new_52w_high' => $this->checkNewExtreme($rule, $prices, $latestDate, high: true),
            'new_52w_low' => $this->checkNewExtreme($rule, $prices, $latestDate, high: false),
            'shariah_status_change' => $this->checkShariahChange($rule, $security->shariahStatuses),
            default => null,
        };
    }

    private function checkPriceChange(AlertRule $rule, Collection $prices, string $latestDate): ?AlertTrigger
    {
        $latestIndex = $this->indexForDate($prices, $latestDate);
        $latest = $latestIndex !== null ? $prices->get($latestIndex) : null;
        $prev = $latestIndex !== null && $latestIndex > 0 ? $prices->get($latestIndex - 1) : null;

        if (! $latest || ! $prev || (float) $prev->close == 0.0) {
            return null;
        }

        $changePct = (($latest->close - $prev->close) / $prev->close) * 100;
        $threshold = (float) $rule->threshold;

        $triggered = match ($rule->direction) {
            'up' => $changePct >= $threshold,
            'down' => $changePct <= -$threshold,
            default => abs($changePct) >= $threshold,
        };

        if (! $triggered) {
            return null;
        }

        $company = $rule->watchlistItem->security->company;
        $direction = $changePct >= 0 ? 'up' : 'down';

        return $this->recordTrigger($rule, $latestDate, sprintf(
            '%s (%s) moved %s %.2f%% to RM%.4f.',
            $company->name, $company->stock_code, $direction, abs($changePct), $latest->close
        ));
    }

    private function checkVolumeSpike(AlertRule $rule, Collection $prices, string $latestDate): ?AlertTrigger
    {
        $latestIndex = $this->indexForDate($prices, $latestDate);
        if ($latestIndex === null || $latestIndex < 5) {
            return null;
        }

        $latest = $prices->get($latestIndex);
        $trailing = $prices->slice(max(0, $latestIndex - 20), 20);
        $avgVolume = $trailing->avg('volume');

        if (! $avgVolume || $avgVolume == 0) {
            return null;
        }

        $multiplier = (float) $latest->volume / $avgVolume;
        if ($multiplier < (float) $rule->threshold) {
            return null;
        }

        $company = $rule->watchlistItem->security->company;

        return $this->recordTrigger($rule, $latestDate, sprintf(
            '%s (%s) volume spiked to %s — %.1fx its recent average.',
            $company->name, $company->stock_code, number_format($latest->volume), $multiplier
        ));
    }

    private function checkNewExtreme(AlertRule $rule, Collection $prices, string $latestDate, bool $high): ?AlertTrigger
    {
        $latestIndex = $this->indexForDate($prices, $latestDate);
        $latest = $latestIndex !== null ? $prices->get($latestIndex) : null;
        if (! $latest) {
            return null;
        }

        $extreme = $high ? $prices->max('close') : $prices->min('close');
        $isExtreme = $high ? (float) $latest->close >= (float) $extreme : (float) $latest->close <= (float) $extreme;

        if (! $isExtreme) {
            return null;
        }

        $company = $rule->watchlistItem->security->company;
        $label = $high ? 'new 52-week high' : 'new 52-week low';

        return $this->recordTrigger($rule, $latestDate, sprintf(
            '%s (%s) hit a %s of RM%.4f.',
            $company->name, $company->stock_code, $label, $latest->close
        ));
    }

    private function checkShariahChange(AlertRule $rule, Collection $statuses): ?AlertTrigger
    {
        $sorted = $statuses->sortByDesc('source_publication_date')->values();
        $current = $sorted->get(0);
        $previous = $sorted->get(1);

        if (! $current || ! $previous || $current->status === $previous->status) {
            return null;
        }

        $company = $rule->watchlistItem->security->company;

        return $this->recordTrigger(
            $rule,
            $current->source_publication_date->toDateString(),
            sprintf(
                '%s (%s) Shariah status changed from %s to %s (SC Malaysia list, %s).',
                $company->name, $company->stock_code, $previous->status, $current->status,
                $current->source_publication_date->toDateString()
            )
        );
    }

    /**
     * Carbon's default string cast doesn't match a bare "Y-m-d" string under loose ('==')
     * comparison (e.g. PriceData::max('trade_date')), so firstWhere()/search() against a
     * raw date string silently miss — compare via toDateString() explicitly instead.
     */
    private function indexForDate(Collection $prices, string $date): ?int
    {
        $index = $prices->search(fn ($p) => $p->trade_date->toDateString() === $date);

        return $index === false ? null : $index;
    }

    private function recordTrigger(AlertRule $rule, string $triggerDate, string $message): ?AlertTrigger
    {
        $existing = AlertTrigger::where('alert_rule_id', $rule->id)->where('trigger_date', $triggerDate)->first();
        if ($existing) {
            return null;
        }

        return AlertTrigger::create([
            'alert_rule_id' => $rule->id,
            'trigger_date' => $triggerDate,
            'message' => $message,
        ]);
    }
}
