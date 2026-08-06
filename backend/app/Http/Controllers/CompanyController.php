<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\TechnicalIndicators;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);

        $query = Company::query()->with(['market', 'sector', 'security']);

        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('stock_code', 'like', "%{$q}%");
            });
        }

        $companies = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'data' => $companies->items(),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
            ],
        ]);
    }

    public function show(string $stockCode)
    {
        $company = Company::with(['market', 'sector', 'security'])
            ->where('stock_code', $stockCode)
            ->firstOrFail();

        return response()->json($company);
    }

    public function fundamentals(Request $request, string $stockCode)
    {
        $company = Company::where('stock_code', $stockCode)->firstOrFail();
        $periodType = $request->query('period_type', 'annual');

        $periods = $company->fundamentalData()
            ->where('period_type', $periodType)
            ->orderBy('period_end')
            ->get();

        $latestPrice = $company->security?->latestPrice;

        $result = $periods->map(function ($f) use ($latestPrice) {
            $eps = (float) $f->eps;
            $bookValue = (float) $f->book_value_per_share;
            $price = $latestPrice ? (float) $latestPrice->close : null;

            return [
                'period_end' => $f->period_end->toDateString(),
                'revenue' => (float) $f->revenue,
                'net_profit' => (float) $f->net_profit,
                'net_margin' => $f->revenue > 0 ? round(($f->net_profit / $f->revenue) * 100, 2) : null,
                'eps' => $eps,
                'roe' => (float) $f->roe,
                'roa' => (float) $f->roa,
                'debt_equity' => (float) $f->debt_equity,
                'current_ratio' => (float) $f->current_ratio,
                'dividend_per_share' => (float) $f->dividend_per_share,
                'book_value_per_share' => $bookValue,
                'pe' => ($price && $eps > 0) ? round($price / $eps, 2) : null,
                'pb' => ($price && $bookValue > 0) ? round($price / $bookValue, 2) : null,
            ];
        });

        return response()->json([
            'stock_code' => $stockCode,
            'period_type' => $periodType,
            'periods' => $result,
        ]);
    }

    public function technicals(Request $request, string $stockCode)
    {
        $company = Company::where('stock_code', $stockCode)->firstOrFail();
        $security = $company->security;

        if (! $security) {
            return response()->json(['stock_code' => $stockCode, 'as_of' => null, 'candles' => [], 'indicators' => []]);
        }

        $query = $security->priceData()->orderBy('trade_date');

        if ($from = $request->query('from')) {
            $query->where('trade_date', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->where('trade_date', '<=', $to);
        }

        $prices = $query->get();
        $closes = $prices->pluck('close')->map(fn ($v) => (float) $v)->values()->all();

        $requestedIndicators = array_filter(explode(',', $request->query('indicators', 'ma,rsi,macd,bbands')));

        $indicators = [];
        if (in_array('ma', $requestedIndicators)) {
            $indicators['ma'] = TechnicalIndicators::sma($closes, 20);
        }
        if (in_array('bbands', $requestedIndicators)) {
            $indicators['bbands'] = TechnicalIndicators::bollingerBands($closes, 20);
        }
        if (in_array('rsi', $requestedIndicators)) {
            $indicators['rsi'] = TechnicalIndicators::rsi($closes, 14);
        }
        if (in_array('macd', $requestedIndicators)) {
            $indicators['macd'] = TechnicalIndicators::macd($closes);
        }

        return response()->json([
            'stock_code' => $stockCode,
            'as_of' => $prices->last()?->trade_date?->toDateString(),
            'candles' => $prices->map(fn ($p) => [
                'date' => $p->trade_date->toDateString(),
                'open' => (float) $p->open,
                'high' => (float) $p->high,
                'low' => (float) $p->low,
                'close' => (float) $p->close,
                'volume' => (int) $p->volume,
            ]),
            'indicators' => $indicators,
        ]);
    }
}
