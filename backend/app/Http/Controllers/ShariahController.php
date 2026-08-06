<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ShariahStatus;
use App\Models\Security;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ShariahController extends Controller
{
    public function status(string $stockCode)
    {
        $company = Company::where('stock_code', $stockCode)->firstOrFail();
        $security = $company->security;

        if (! $security) {
            return response()->json(['stock_code' => $stockCode, 'current' => null, 'history' => []]);
        }

        $history = $security->shariahStatuses; // already ordered desc by source_publication_date

        return response()->json([
            'stock_code' => $stockCode,
            'current' => $history->first() ? [
                'status' => $history->first()->status,
                'source_publication_date' => $history->first()->source_publication_date->toDateString(),
            ] : null,
            'history' => $history->map(fn ($h) => [
                'status' => $h->status,
                'source_publication_date' => $h->source_publication_date->toDateString(),
            ]),
        ]);
    }

    public function securities(Request $request)
    {
        $status = $request->query('status');

        $securities = Security::query()
            ->with(['company', 'currentShariahStatus'])
            ->get()
            ->filter(fn (Security $s) => $s->currentShariahStatus !== null)
            ->when($status, fn ($c) => $c->filter(fn (Security $s) => $s->currentShariahStatus->status === $status))
            ->map(fn (Security $s) => [
                'stock_code' => $s->company->stock_code,
                'name' => $s->company->name,
                'status' => $s->currentShariahStatus->status,
            ])->values();

        return response()->json($securities);
    }

    /** Admin: upload SC Malaysia list CSV for preview (per FR-SHR-5 / ADR-0003 / UC-7). */
    public function import(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);

        $rows = array_map('str_getcsv', file($request->file('file')->getRealPath()));
        $header = array_map('trim', array_shift($rows) ?? []);

        $stockCodeIdx = array_search('stock_code', $header);
        $statusIdx = array_search('status', $header);
        $dateIdx = array_search('source_publication_date', $header);

        if ($stockCodeIdx === false || $statusIdx === false || $dateIdx === false) {
            return response()->json([
                'message' => 'Malformed file: expected columns stock_code, status, source_publication_date.',
            ], 422);
        }

        $changes = [];
        $parsed = [];

        foreach ($rows as $row) {
            if (! isset($row[$stockCodeIdx]) || trim($row[$stockCodeIdx]) === '') {
                continue;
            }
            $stockCode = trim($row[$stockCodeIdx]);
            $status = trim($row[$statusIdx]);
            $publicationDate = trim($row[$dateIdx]);

            $company = Company::where('stock_code', $stockCode)->first();
            $security = $company?->security;
            $oldStatus = $security?->currentShariahStatus?->status;

            $parsed[] = [
                'stock_code' => $stockCode,
                'status' => $status,
                'source_publication_date' => $publicationDate,
                'security_id' => $security?->id,
            ];

            if ($security && $oldStatus !== $status) {
                $changes[] = [
                    'stock_code' => $stockCode,
                    'name' => $company->name,
                    'old_status' => $oldStatus ?? 'unclassified',
                    'new_status' => $status,
                ];
            }
        }

        $importId = (string) Str::uuid();
        Cache::put("shariah_import:{$importId}", $parsed, now()->addMinutes(30));

        return response()->json([
            'import_id' => $importId,
            'total_parsed' => count($parsed),
            'changes' => $changes,
        ]);
    }

    /** Admin: commit a previewed import (per FR-SHR-5 / UC-7). */
    public function commitImport(Request $request, string $importId)
    {
        $parsed = Cache::get("shariah_import:{$importId}");

        if (! $parsed) {
            return response()->json(['message' => 'Import preview expired or not found. Please re-upload.'], 404);
        }

        $now = now();
        $count = 0;

        foreach ($parsed as $row) {
            if (! $row['security_id']) {
                continue;
            }
            ShariahStatus::create([
                'security_id' => $row['security_id'],
                'status' => $row['status'],
                'source_publication_date' => $row['source_publication_date'],
                'imported_at' => $now,
                'imported_by_user_id' => $request->user()->id,
            ]);
            $count++;
        }

        Cache::forget("shariah_import:{$importId}");

        return response()->json(['message' => 'Import committed.', 'records_committed' => $count]);
    }
}
