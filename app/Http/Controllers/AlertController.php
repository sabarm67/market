<?php

namespace App\Http\Controllers;

use App\Models\AlertRule;
use App\Models\AlertTrigger;
use App\Models\WatchlistItem;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $rules = AlertRule::query()
            ->whereHas('watchlistItem.watchlist', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with('watchlistItem.security.company')
            ->get();

        return response()->json($rules->map(fn (AlertRule $r) => $this->transformRule($r)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'watchlist_item_id' => ['required', 'exists:watchlist_items,id'],
            'type' => ['required', 'in:price_change_pct,volume_spike,new_52w_high,new_52w_low,shariah_status_change'],
            'direction' => ['nullable', 'in:up,down,either'],
            'threshold' => ['nullable', 'numeric', 'min:0'],
        ]);

        $item = WatchlistItem::with('watchlist')->findOrFail($validated['watchlist_item_id']);
        abort_if($item->watchlist->user_id !== $request->user()->id, 403);

        if (in_array($validated['type'], ['price_change_pct', 'volume_spike']) && ! isset($validated['threshold'])) {
            return response()->json(['message' => 'threshold is required for this alert type.'], 422);
        }

        $rule = AlertRule::create($validated + ['active' => true]);

        return response()->json($this->transformRule($rule->load('watchlistItem.security.company')), 201);
    }

    public function update(Request $request, AlertRule $rule)
    {
        abort_if($rule->watchlistItem->watchlist->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'active' => ['sometimes', 'boolean'],
            'threshold' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'direction' => ['sometimes', 'nullable', 'in:up,down,either'],
        ]);

        $rule->update($validated);

        return response()->json($this->transformRule($rule->fresh('watchlistItem.security.company')));
    }

    public function destroy(Request $request, AlertRule $rule)
    {
        abort_if($rule->watchlistItem->watchlist->user_id !== $request->user()->id, 403);

        $rule->delete();

        return response()->noContent();
    }

    public function triggers(Request $request)
    {
        $triggers = AlertTrigger::query()
            ->whereHas('alertRule.watchlistItem.watchlist', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with('alertRule.watchlistItem.security.company')
            ->orderByDesc('trigger_date')
            ->limit(50)
            ->get();

        return response()->json($triggers->map(fn (AlertTrigger $t) => [
            'id' => $t->id,
            'stock_code' => $t->alertRule->watchlistItem->security->company->stock_code,
            'type' => $t->alertRule->type,
            'trigger_date' => $t->trigger_date->toDateString(),
            'message' => $t->message,
            'read_at' => $t->read_at,
        ]));
    }

    public function markTriggerRead(Request $request, AlertTrigger $trigger)
    {
        abort_if($trigger->alertRule->watchlistItem->watchlist->user_id !== $request->user()->id, 403);

        $trigger->update(['read_at' => now()]);

        return response()->noContent();
    }

    private function transformRule(AlertRule $rule): array
    {
        return [
            'id' => $rule->id,
            'watchlist_item_id' => $rule->watchlist_item_id,
            'stock_code' => $rule->watchlistItem->security->company->stock_code,
            'name' => $rule->watchlistItem->security->company->name,
            'type' => $rule->type,
            'direction' => $rule->direction,
            'threshold' => $rule->threshold,
            'active' => $rule->active,
        ];
    }
}
