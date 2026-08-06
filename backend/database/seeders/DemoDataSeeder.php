<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\FundamentalData;
use App\Models\Market;
use App\Models\PriceData;
use App\Models\Sector;
use App\Models\Security;
use App\Models\ShariahStatus;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Demo/reference data for local development, since no live market data provider is
 * contracted yet (Dependency D1 in assumptions-dependencies-risks.md). Not real Bursa
 * Malaysia data — illustrative only.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $market = Market::create(['name' => 'Bursa Malaysia', 'sub_market' => 'Main']);

        $sectors = collect([
            ['name' => 'Technology', 'industry' => 'Software & Services'],
            ['name' => 'Financial Services', 'industry' => 'Banking'],
            ['name' => 'Consumer Products', 'industry' => 'Retail'],
            ['name' => 'Industrial Products', 'industry' => 'Manufacturing'],
            ['name' => 'Plantation', 'industry' => 'Agriculture'],
        ])->map(fn ($s) => Sector::create($s));

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $demoUser = User::create([
            'name' => 'Demo Investor',
            'email' => 'investor@example.com',
            'password' => Hash::make('password'),
            'role' => 'registered',
        ]);

        $companies = [
            ['stock_code' => '1001', 'name' => 'ABC Technology Berhad', 'sector' => 'Technology', 'seed_price' => 3.20],
            ['stock_code' => '1002', 'name' => 'Delta Software Holdings', 'sector' => 'Technology', 'seed_price' => 1.85],
            ['stock_code' => '2001', 'name' => 'Maju Bank Berhad', 'sector' => 'Financial Services', 'seed_price' => 8.90],
            ['stock_code' => '2002', 'name' => 'Amanah Finance Group', 'sector' => 'Financial Services', 'seed_price' => 4.10],
            ['stock_code' => '3001', 'name' => 'Selera Consumer Products', 'sector' => 'Consumer Products', 'seed_price' => 2.35],
            ['stock_code' => '3002', 'name' => 'Ceria Retail Holdings', 'sector' => 'Consumer Products', 'seed_price' => 1.10],
            ['stock_code' => '4001', 'name' => 'Perkasa Industries Berhad', 'sector' => 'Industrial Products', 'seed_price' => 5.60],
            ['stock_code' => '4002', 'name' => 'Wawasan Manufacturing', 'sector' => 'Industrial Products', 'seed_price' => 0.95],
            ['stock_code' => '5001', 'name' => 'Ladang Sawit Berhad', 'sector' => 'Plantation', 'seed_price' => 6.75],
            ['stock_code' => '5002', 'name' => 'Hijau Plantations Holdings', 'sector' => 'Plantation', 'seed_price' => 3.40],
        ];

        $shariahPublicationDates = ['2025-05-30', '2025-11-28', '2026-05-29'];

        foreach ($companies as $index => $c) {
            $sector = $sectors->firstWhere('name', $c['sector']);

            $company = Company::create([
                'market_id' => $market->id,
                'sector_id' => $sector->id,
                'name' => $c['name'],
                'stock_code' => $c['stock_code'],
                'overview' => "{$c['name']} is a Bursa Malaysia-listed company in the {$c['sector']} sector. (Demo data — not a real company.)",
                'business_segments' => 'Core operations, ancillary services.',
                'listing_date' => now()->subYears(5 + $index % 10)->format('Y-m-d'),
                'management' => [
                    ['name' => 'Ahmad Zulkifli', 'title' => 'Chief Executive Officer'],
                    ['name' => 'Lim Mei Ling', 'title' => 'Chief Financial Officer'],
                ],
                'major_shareholders' => [
                    ['name' => 'Employees Provident Fund', 'holding_pct' => 12.4],
                    ['name' => 'Amanah Saham Nasional Berhad', 'holding_pct' => 8.1],
                    ['name' => 'Founder & Family', 'holding_pct' => 22.6],
                ],
            ]);

            $security = Security::create([
                'company_id' => $company->id,
                'ticker' => $c['stock_code'],
                'type' => 'ordinary_shares',
            ]);

            $this->seedPriceHistory($security, (float) $c['seed_price']);
            $this->seedFundamentals($company);

            // Alternate compliant/non-compliant, with one status flip across publication dates for variety.
            $baseStatus = $index % 4 === 3 ? 'non_compliant' : 'compliant';
            foreach ($shariahPublicationDates as $i => $date) {
                $status = ($index % 5 === 0 && $i === count($shariahPublicationDates) - 1)
                    ? ($baseStatus === 'compliant' ? 'non_compliant' : 'compliant')
                    : $baseStatus;

                ShariahStatus::create([
                    'security_id' => $security->id,
                    'status' => $status,
                    'source_publication_date' => $date,
                    'imported_at' => $date,
                    'imported_by_user_id' => $admin->id,
                ]);
            }
        }

        $watchlist = Watchlist::create(['user_id' => $demoUser->id, 'name' => 'Tech Picks']);
        $techSecurities = Security::whereHas('company', fn ($q) => $q->whereIn('stock_code', ['1001', '1002']))->get();
        foreach ($techSecurities as $security) {
            $watchlist->items()->create([
                'security_id' => $security->id,
                'note' => 'Watching for Q3 results.',
                'added_at' => now(),
            ]);
        }
    }

    private function seedPriceHistory(Security $security, float $seedPrice): void
    {
        $date = now()->subYear()->startOfDay();
        $end = now()->startOfDay();
        $price = $seedPrice;
        $rows = [];

        while ($date->lte($end)) {
            if ($date->isWeekday()) {
                $drift = (mt_rand(-300, 320) / 10000); // ~-3% to +3.2% daily drift
                $open = $price;
                $close = max(0.10, round($price * (1 + $drift), 4));
                $high = round(max($open, $close) * (1 + mt_rand(0, 150) / 10000), 4);
                $low = round(min($open, $close) * (1 - mt_rand(0, 150) / 10000), 4);
                $volume = mt_rand(50_000, 2_500_000);

                $rows[] = [
                    'security_id' => $security->id,
                    'trade_date' => $date->format('Y-m-d'),
                    'open' => $open,
                    'high' => $high,
                    'low' => $low,
                    'close' => $close,
                    'volume' => $volume,
                    'ingested_at' => now(),
                ];

                $price = $close;
            }
            $date->addDay();
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            PriceData::insert($chunk);
        }
    }

    private function seedFundamentals(Company $company): void
    {
        $baseRevenue = mt_rand(80, 900) * 1_000_000;

        for ($yearsAgo = 3; $yearsAgo >= 0; $yearsAgo--) {
            $periodEnd = now()->subYears($yearsAgo)->endOfYear()->format('Y-m-d');
            $growth = 1 + (mt_rand(-5, 15) / 100);
            $revenue = round($baseRevenue * ($growth ** (3 - $yearsAgo)), 2);
            $netMarginPct = mt_rand(6, 22) / 100;
            $netProfit = round($revenue * $netMarginPct, 2);
            $sharesOutstanding = mt_rand(200, 900) * 1_000_000;

            FundamentalData::create([
                'company_id' => $company->id,
                'period_type' => 'annual',
                'period_end' => $periodEnd,
                'revenue' => $revenue,
                'net_profit' => $netProfit,
                'eps' => round($netProfit / $sharesOutstanding, 4),
                'book_value_per_share' => round(mt_rand(80, 400) / 100, 4),
                'roe' => round(mt_rand(4, 22) + mt_rand(0, 99) / 100, 3),
                'roa' => round(mt_rand(2, 14) + mt_rand(0, 99) / 100, 3),
                'debt_equity' => round(mt_rand(10, 90) / 100, 3),
                'current_ratio' => round(mt_rand(90, 250) / 100, 3),
                'dividend_per_share' => round(mt_rand(1, 15) / 100, 4),
                'ingested_at' => now(),
            ]);
        }
    }
}
