<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Market;
use App\Models\PriceData;
use App\Models\Security;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Pulls real Bursa Malaysia company list + latest EOD prices from EODHD (Dependency D1).
 * Two bulk endpoints, no per-symbol looping.
 *
 * ponytail: sector left null and fundamentals aren't synced — EODHD has no bulk
 * fundamentals endpoint, only per-symbol (~1000+ calls). Add a separate
 * `market:sync-fundamentals {code}` command if/when that's needed.
 */
class SyncMarketData extends Command
{
    protected $signature = 'market:sync';

    protected $description = 'Sync Bursa Malaysia company list and latest EOD prices from EODHD';

    public function handle(): int
    {
        $key = config('services.eodhd.key');
        if (! $key) {
            $this->error('EODHD_API_KEY not set.');

            return self::FAILURE;
        }

        $market = Market::firstOrCreate(['name' => 'Bursa Malaysia', 'sub_market' => 'Main']);

        $symbols = Http::get('https://eodhd.com/api/exchange-symbol-list/KLSE', [
            'api_token' => $key,
            'fmt' => 'json',
        ])->throw()->json();

        $tickerToSecurity = [];
        foreach ($symbols as $s) {
            if (($s['Type'] ?? null) !== 'Common Stock' || empty($s['Code'])) {
                continue;
            }

            $company = Company::updateOrCreate(
                ['stock_code' => $s['Code']],
                ['name' => $s['Name'], 'market_id' => $market->id]
            );

            $security = Security::firstOrCreate(
                ['ticker' => $s['Code']],
                ['company_id' => $company->id]
            );

            $tickerToSecurity[$s['Code']] = $security->id;
        }
        $this->info(count($tickerToSecurity).' companies synced.');

        $prices = Http::get('https://eodhd.com/api/eod-bulk-last-day/KLSE', [
            'api_token' => $key,
            'fmt' => 'json',
        ])->throw()->json();

        $priceCount = 0;
        foreach ($prices as $p) {
            $securityId = $tickerToSecurity[$p['code']] ?? null;
            if (! $securityId) {
                continue;
            }

            PriceData::updateOrCreate(
                ['security_id' => $securityId, 'trade_date' => $p['date']],
                [
                    'open' => $p['open'], 'high' => $p['high'], 'low' => $p['low'], 'close' => $p['close'],
                    'volume' => $p['volume'] ?? 0, 'ingested_at' => now(),
                ]
            );
            $priceCount++;
        }
        $this->info("{$priceCount} prices synced.");

        return self::SUCCESS;
    }
}
