<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained('markets');
            $table->foreignId('sector_id')->nullable()->constrained('sectors');
            $table->string('name');
            $table->string('stock_code', 20)->unique();
            $table->text('overview')->nullable();
            $table->text('business_segments')->nullable();
            $table->date('listing_date')->nullable();
            $table->json('management')->nullable(); // [{name, title}, ...] per FR-COMP-3
            $table->json('major_shareholders')->nullable(); // [{name, holding_pct}, ...] per FR-COMP-4
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
