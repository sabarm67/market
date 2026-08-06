<?php

namespace App\Http\Controllers;

use App\Models\FundamentalData;
use App\Models\PriceData;
use App\Models\Security;
use App\Models\Sector;
use App\Services\TechnicalIndicators;
use Illuminate\Http\Request;

class ScreenerController extends Controller
{
    /**
     * Multi-criteria stock screener. Custom formula builder (per the original spec) is
     * explicitly deferred — see future-enhancements-roadmap.md.
     */
    public function index(Request $request)
    {
        $securities = Security::query()
            ->with(['company.sector', 'company.market'])
            ->when($request->query('sector_id'), fn ($q, $v) => $q->whereHas('company', fn ($c) => $c->where('sector_id', $v)))
            ->when($request->query('sub_market'), fn ($q, $v) => $q->whereHas('company.market', fn ($m) => $m->where('sub_market', $v)))
            ->get();

        $securityIds = $securities->pluck('id');
        $companyIds = $securities->pluck('company.id');

        $latestPrices = PriceData::whereIn('security_id', $securityIds)
            ->whereIn('id', function ($q) use ($securityIds) {
                $q->selectRaw('MAX(id)')->from('price_data')->whereIn('security_id', $securityIds)->groupBy('security_id');
            })
            ->get()->keyBy('security_id');

        $priceSeries = PriceData::whereIn('security_id', $securityIds)
            ->where('trade_date', '>=', now()->subDays(90))
            ->orderBy('trade_date')
            ->get()->groupBy('security_id');

        $shariahStatuses = Security::whereIn('id', $securityIds)
            ->with('currentShariahStatus')
            ->get()->keyBy('id');

        $annualFundamentals = FundamentalData::whereIn('company_id', $companyIds)
            ->where('period_type', 'annual')
            ->orderByDesc('period_end')
            ->get()->groupBy('company_id');

        $rows = $securities->map(function (Security $security) use ($latestPrices, $priceSeries, $shariahStatuses, $annualFundamentals) {
            $company = $security->company;
            $price = $latestPrices->get($security->id);
            $fundamentals = $annualFundamentals->get($company->id, collect());
            $latestFundamental = $fundamentals->first();
            $priorFundamental = $fundamentals->get(1);

            if (! $price || ! $latestFundamental) {
                return null;
            }

            $closePrice = (float) $price->close;
            $eps = (float) $latestFundamental->eps;
            $bookValue = (float) $latestFundamental->book_value_per_share;
            $sharesOutstanding = $latestFundamental->shares_outstanding;
            $dividendPerShare = (float) $latestFundamental->dividend_per_share;

            $closes = ($priceSeries->get($security->id) ?? collect())->pluck('close')->map(fn ($v) => (float) $v)->values()->all();
            $rsiSeries = TechnicalIndicators::rsi($closes, 14);
            $rsi = end($rsiSeries) ?: null;

            return [
                'stock_code' => $company->stock_code,
                'name' => $company->name,
                'sector' => $company->sector?->name,
                'sub_market' => $company->market?->sub_market,
                'shariah_status' => $shariahStatuses->get($security->id)?->currentShariahStatus?->status,
                'price' => $closePrice,
                'volume' => (int) $price->volume,
                'market_cap' => $sharesOutstanding ? round($closePrice * $sharesOutstanding, 2) : null,
                'pe' => $eps > 0 ? round($closePrice / $eps, 2) : null,
                'pb' => $bookValue > 0 ? round($closePrice / $bookValue, 2) : null,
                'roe' => (float) $latestFundamental->roe,
                'debt_equity' => (float) $latestFundamental->debt_equity,
                'dividend_yield' => $closePrice > 0 ? round(($dividendPerShare / $closePrice) * 100, 2) : null,
                'revenue_growth_pct' => ($priorFundamental && (float) $priorFundamental->revenue > 0)
                    ? round((($latestFundamental->revenue - $priorFundamental->revenue) / $priorFundamental->revenue) * 100, 2)
                    : null,
                'rsi' => $rsi,
            ];
        })->filter()->values();

        $filtered = $rows
            ->when($request->query('shariah_status'), fn ($c, $v) => $c->where('shariah_status', $v))
            ->when($request->query('market_cap_min'), fn ($c, $v) => $c->filter(fn ($r) => $r['market_cap'] !== null && $r['market_cap'] >= $v))
            ->when($request->query('market_cap_max'), fn ($c, $v) => $c->filter(fn ($r) => $r['market_cap'] !== null && $r['market_cap'] <= $v))
            ->when($request->query('pe_min'), fn ($c, $v) => $c->filter(fn ($r) => $r['pe'] !== null && $r['pe'] >= $v))
            ->when($request->query('pe_max'), fn ($c, $v) => $c->filter(fn ($r) => $r['pe'] !== null && $r['pe'] <= $v))
            ->when($request->query('pb_min'), fn ($c, $v) => $c->filter(fn ($r) => $r['pb'] !== null && $r['pb'] >= $v))
            ->when($request->query('pb_max'), fn ($c, $v) => $c->filter(fn ($r) => $r['pb'] !== null && $r['pb'] <= $v))
            ->when($request->query('roe_min'), fn ($c, $v) => $c->filter(fn ($r) => $r['roe'] >= $v))
            ->when($request->query('dividend_yield_min'), fn ($c, $v) => $c->filter(fn ($r) => $r['dividend_yield'] !== null && $r['dividend_yield'] >= $v))
            ->when($request->query('debt_equity_max'), fn ($c, $v) => $c->filter(fn ($r) => $r['debt_equity'] <= $v))
            ->when($request->query('revenue_growth_min'), fn ($c, $v) => $c->filter(fn ($r) => $r['revenue_growth_pct'] !== null && $r['revenue_growth_pct'] >= $v))
            ->when($request->query('volume_min'), fn ($c, $v) => $c->filter(fn ($r) => $r['volume'] >= $v))
            ->when($request->query('rsi_min'), fn ($c, $v) => $c->filter(fn ($r) => $r['rsi'] !== null && $r['rsi'] >= $v))
            ->when($request->query('rsi_max'), fn ($c, $v) => $c->filter(fn ($r) => $r['rsi'] !== null && $r['rsi'] <= $v))
            ->values();

        $sortableFields = ['market_cap', 'pe', 'pb', 'roe', 'dividend_yield', 'debt_equity', 'revenue_growth_pct', 'volume', 'rsi'];
        $sortBy = in_array($request->query('sort_by'), $sortableFields) ? $request->query('sort_by') : 'stock_code';
        $sortDir = $request->query('sort_dir') === 'desc' ? 'desc' : 'asc';

        $sorted = $sortDir === 'desc' ? $filtered->sortByDesc($sortBy) : $filtered->sortBy($sortBy);

        return response()->json([
            'total' => $sorted->count(),
            'results' => $sorted->values(),
        ]);
    }

    public function sectors()
    {
        return response()->json(Sector::orderBy('name')->get(['id', 'name', 'industry']));
    }
}
