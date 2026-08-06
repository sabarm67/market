<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watchlist_item_id')->constrained('watchlist_items')->cascadeOnDelete();
            $table->string('type', 30); // 'price_change_pct', 'volume_spike', 'new_52w_high', 'new_52w_low', 'shariah_status_change'
            $table->string('direction', 10)->nullable(); // 'up', 'down', 'either' — applies to price_change_pct only
            $table->decimal('threshold', 10, 4)->nullable(); // % for price_change_pct, multiplier for volume_spike
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_rules');
    }
};
