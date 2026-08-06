<?php

namespace App\Services;

use App\Models\Portfolio;
use Illuminate\Support\Collection;

/**
 * Computes holdings from a portfolio's buy/sell transaction ledger using the average
 * cost method (per ADR-0010) — not FIFO/LIFO lot tracking. See FRS Module 9.
 */
class PortfolioCalculationService
{
    public function summarize(Portfolio $portfolio): array
    {
        $transactions = $portfolio->transactions()->with('security.company.sector')->get();
        $bySecurity = $transactions->groupBy('security_id');

        $holdings = collect();
        $totalRealizedGain = 0.0;

        foreach ($bySecurity as $securityId => $txns) {
            $result = $this->processTransactions($txns);
            $totalRealizedGain += $result['realized_gain'];

            if ($result['quantity'] <= 0) {
                continue;
            }

            $security = $txns->first()->security;
            $latestPrice = $security->latestPrice?->close;
            $marketValue = $latestPrice ? $result['quantity'] * (float) $latestPrice : null;
            $unrealizedGain = $marketValue !== null ? $marketValue - $result['cost_basis'] : null;

            $holdings->push([
                'stock_code' => $security->company->stock_code,
                'name' => $security->company->name,
                'sector' => $security->company->sector?->name,
                'quantity' => $result['quantity'],
                'avg_cost' => $result['quantity'] > 0 ? round($result['cost_basis'] / $result['quantity'], 4) : null,
                'cost_basis' => round($result['cost_basis'], 2),
                'latest_price' => $latestPrice ? (float) $latestPrice : null,
                'market_value' => $marketValue !== null ? round($marketValue, 2) : null,
                'unrealized_gain' => $unrealizedGain !== null ? round($unrealizedGain, 2) : null,
                'unrealized_gain_pct' => ($unrealizedGain !== null && $result['cost_basis'] > 0)
                    ? round(($unrealizedGain / $result['cost_basis']) * 100, 2)
                    : null,
                'realized_gain' => round($result['realized_gain'], 2),
            ]);
        }

        $totalMarketValue = $holdings->sum('market_value');
        $totalCostBasis = $holdings->sum('cost_basis');
        $totalUnrealizedGain = $totalMarketValue - $totalCostBasis;

        $sectorAllocation = $holdings
            ->filter(fn ($h) => $h['sector'] && $h['market_value'])
            ->groupBy('sector')
            ->map(fn (Collection $group, $sector) => [
                'sector' => $sector,
                'market_value' => round($group->sum('market_value'), 2),
                'pct' => $totalMarketValue > 0 ? round(($group->sum('market_value') / $totalMarketValue) * 100, 2) : 0,
            ])->values();

        return [
            'holdings' => $holdings->map(fn ($h) => $h + [
                'allocation_pct' => $totalMarketValue > 0 && $h['market_value'] !== null
                    ? round(($h['market_value'] / $totalMarketValue) * 100, 2)
                    : null,
            ])->values(),
            'sector_allocation' => $sectorAllocation,
            'totals' => [
                'market_value' => round($totalMarketValue, 2),
                'cost_basis' => round($totalCostBasis, 2),
                'unrealized_gain' => round($totalUnrealizedGain, 2),
                'unrealized_gain_pct' => $totalCostBasis > 0 ? round(($totalUnrealizedGain / $totalCostBasis) * 100, 2) : null,
                'realized_gain' => round($totalRealizedGain, 2),
            ],
        ];
    }

    /** @param Collection<int, \App\Models\PortfolioTransaction> $transactions ordered chronologically */
    private function processTransactions(Collection $transactions): array
    {
        $quantity = 0.0;
        $costBasis = 0.0;
        $realizedGain = 0.0;

        foreach ($transactions->sortBy('transaction_date') as $txn) {
            $qty = (float) $txn->quantity;
            $price = (float) $txn->price;

            if ($txn->type === 'buy') {
                $costBasis += $qty * $price;
                $quantity += $qty;
            } else { // sell
                $avgCost = $quantity > 0 ? $costBasis / $quantity : 0;
                $sellQty = min($qty, $quantity); // can't sell more than held
                $realizedGain += ($price - $avgCost) * $sellQty;
                $costBasis -= $avgCost * $sellQty;
                $quantity -= $sellQty;
            }
        }

        return ['quantity' => $quantity, 'cost_basis' => max(0, $costBasis), 'realized_gain' => $realizedGain];
    }
}
