<?php

namespace App\Http\Controllers;

use App\Models\PriceData;
use App\Models\Security;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $latestDate = PriceData::max('trade_date');

        if (! $latestDate) {
            return response()->json([
                'as_of' => null,
                'top_gainers' => [],
                'top_losers' => [],
                'top_volume' => [],
                'sector_performance' => [],
                'breadth' => ['advancers' => 0, 'decliners' => 0, 'new_highs_52w' => 0, 'new_lows_52w' => 0],
            ]);
        }

        $prevDate = PriceData::where('trade_date', '<', $latestDate)->max('trade_date');

        $securities = Security::query()
            ->with(['company.sector', 'company.market'])
            ->when($request->query('sub_market'), function ($q, $subMarket) {
                $q->whereHas('company.market', fn ($m) => $m->where('sub_market', $subMarket));
            })
            ->when($request->query('sector_id'), function ($q, $sectorId) {
                $q->whereHas('company', fn ($c) => $c->where('sector_id', $sectorId));
            })
            ->get();

        $latestPrices = PriceData::whereIn('security_id', $securities->pluck('id'))
            ->where('trade_date', $latestDate)
            ->get()->keyBy('security_id');

        $prevPrices = $prevDate
            ? PriceData::whereIn('security_id', $securities->pluck('id'))
                ->where('trade_date', $prevDate)
                ->get()->keyBy('security_id')
            : collect();

        // All-time high/low per security (demo-data scope; approximates 52-week high/low, see NFR notes).
        $extremes = PriceData::whereIn('security_id', $securities->pluck('id'))
            ->selectRaw('security_id, MAX(close) as max_close, MIN(close) as min_close')
            ->groupBy('security_id')
            ->get()->keyBy('security_id');

        $rows = $securities->map(function (Security $security) use ($latestPrices, $prevPrices, $extremes) {
            $latest = $latestPrices->get($security->id);
            $prev = $prevPrices->get($security->id);

            if (! $latest) {
                return null;
            }

            $changePct = $prev && (float) $prev->close !== 0.0
                ? round((($latest->close - $prev->close) / $prev->close) * 100, 2)
                : null;

            $extreme = $extremes->get($security->id);

            return [
                'stock_code' => $security->company->stock_code,
                'name' => $security->company->name,
                'sector' => $security->company->sector?->name,
                'change_pct' => $changePct,
                'volume' => (int) $latest->volume,
                'is_new_high' => $extreme && (float) $latest->close >= (float) $extreme->max_close,
                'is_new_low' => $extreme && (float) $latest->close <= (float) $extreme->min_close,
            ];
        })->filter()->values();

        $topGainers = $rows->filter(fn ($r) => $r['change_pct'] !== null)->sortByDesc('change_pct')->take(10)->values();
        $topLosers = $rows->filter(fn ($r) => $r['change_pct'] !== null)->sortBy('change_pct')->take(10)->values();
        $topVolume = $rows->sortByDesc('volume')->take(10)->values();

        $sectorPerformance = $rows->filter(fn ($r) => $r['sector'] && $r['change_pct'] !== null)
            ->groupBy('sector')
            ->map(fn ($group, $sector) => [
                'sector' => $sector,
                'change_pct' => round($group->avg('change_pct'), 2),
            ])->values();

        return response()->json([
            'as_of' => $latestDate,
            'top_gainers' => $topGainers,
            'top_losers' => $topLosers,
            'top_volume' => $topVolume,
            'sector_performance' => $sectorPerformance,
            'breadth' => [
                'advancers' => $rows->filter(fn ($r) => ($r['change_pct'] ?? 0) > 0)->count(),
                'decliners' => $rows->filter(fn ($r) => ($r['change_pct'] ?? 0) < 0)->count(),
                'new_highs_52w' => $rows->filter(fn ($r) => $r['is_new_high'])->count(),
                'new_lows_52w' => $rows->filter(fn ($r) => $r['is_new_low'])->count(),
            ],
        ]);
    }
}
