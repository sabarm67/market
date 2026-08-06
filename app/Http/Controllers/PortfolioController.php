<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\PortfolioTransaction;
use App\Services\PortfolioCalculationService;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function __construct(private PortfolioCalculationService $calculator) {}

    public function index(Request $request)
    {
        $portfolios = $request->user()->portfolios;

        return response()->json($portfolios->map(function (Portfolio $p) {
            $summary = $this->calculator->summarize($p);

            return [
                'id' => $p->id,
                'name' => $p->name,
                'market_value' => $summary['totals']['market_value'],
                'unrealized_gain' => $summary['totals']['unrealized_gain'],
                'unrealized_gain_pct' => $summary['totals']['unrealized_gain_pct'],
            ];
        }));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:100']]);

        $portfolio = $request->user()->portfolios()->create($validated);

        return response()->json(['id' => $portfolio->id, 'name' => $portfolio->name], 201);
    }

    public function destroy(Request $request, Portfolio $portfolio)
    {
        abort_if($portfolio->user_id !== $request->user()->id, 403);

        $portfolio->delete();

        return response()->noContent();
    }

    public function show(Request $request, Portfolio $portfolio)
    {
        abort_if($portfolio->user_id !== $request->user()->id, 403);

        $summary = $this->calculator->summarize($portfolio);

        return response()->json([
            'id' => $portfolio->id,
            'name' => $portfolio->name,
            ...$summary,
            'transactions' => $portfolio->transactions->load('security.company')->map(fn (PortfolioTransaction $t) => [
                'id' => $t->id,
                'stock_code' => $t->security->company->stock_code,
                'name' => $t->security->company->name,
                'type' => $t->type,
                'quantity' => (float) $t->quantity,
                'price' => (float) $t->price,
                'transaction_date' => $t->transaction_date->toDateString(),
                'notes' => $t->notes,
            ])->values(),
        ]);
    }

    public function addTransaction(Request $request, Portfolio $portfolio)
    {
        abort_if($portfolio->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'security_id' => ['required', 'exists:securities,id'],
            'type' => ['required', 'in:buy,sell'],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
            'price' => ['required', 'numeric', 'min:0'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $transaction = $portfolio->transactions()->create($validated + ['created_at' => now()]);

        return response()->json(['id' => $transaction->id], 201);
    }

    public function removeTransaction(Request $request, Portfolio $portfolio, PortfolioTransaction $transaction)
    {
        abort_if($portfolio->user_id !== $request->user()->id, 403);
        abort_if($transaction->portfolio_id !== $portfolio->id, 404);

        $transaction->delete();

        return response()->noContent();
    }
}
