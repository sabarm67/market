<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Models\WatchlistItem;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function index(Request $request)
    {
        $watchlists = $request->user()->watchlists()
            ->with(['items.security.company', 'items.security.currentShariahStatus', 'items.security.latestPrice'])
            ->get();

        return response()->json($watchlists->map(fn ($w) => $this->transform($w)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:100']]);

        $watchlist = $request->user()->watchlists()->create($validated);

        return response()->json($this->transform($watchlist), 201);
    }

    public function destroy(Request $request, Watchlist $watchlist)
    {
        abort_if($watchlist->user_id !== $request->user()->id, 403);

        $watchlist->delete();

        return response()->noContent();
    }

    public function addItem(Request $request, Watchlist $watchlist)
    {
        abort_if($watchlist->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'security_id' => ['required', 'exists:securities,id'],
            'note' => ['nullable', 'string'],
        ]);

        $item = $watchlist->items()->create([
            'security_id' => $validated['security_id'],
            'note' => $validated['note'] ?? null,
            'added_at' => now(),
        ]);

        return response()->json($item->load('security.company'), 201);
    }

    public function removeItem(Request $request, Watchlist $watchlist, WatchlistItem $item)
    {
        abort_if($watchlist->user_id !== $request->user()->id, 403);
        abort_if($item->watchlist_id !== $watchlist->id, 404);

        $item->delete();

        return response()->noContent();
    }

    private function transform(Watchlist $watchlist): array
    {
        return [
            'id' => $watchlist->id,
            'name' => $watchlist->name,
            'items' => $watchlist->items->map(fn (WatchlistItem $item) => [
                'id' => $item->id,
                'stock_code' => $item->security->company->stock_code,
                'name' => $item->security->company->name,
                'price' => $item->security->latestPrice?->close,
                'shariah_status' => $item->security->currentShariahStatus?->status,
                'note' => $item->note,
            ]),
        ];
    }
}
