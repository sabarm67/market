<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Needed for market cap (price * shares_outstanding) in the Stock Screener —
     * not part of MVP's fundamentals display, which stores EPS/book-value-per-share
     * directly rather than deriving them from shares outstanding.
     */
    public function up(): void
    {
        Schema::table('fundamental_data', function (Blueprint $table) {
            $table->unsignedBigInteger('shares_outstanding')->nullable()->after('dividend_per_share');
        });
    }

    public function down(): void
    {
        Schema::table('fundamental_data', function (Blueprint $table) {
            $table->dropColumn('shares_outstanding');
        });
    }
};
