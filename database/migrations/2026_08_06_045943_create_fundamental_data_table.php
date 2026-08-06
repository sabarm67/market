<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fundamental_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('period_type', 10); // 'quarterly', 'annual'
            $table->date('period_end');
            $table->decimal('revenue', 18, 2)->nullable();
            $table->decimal('net_profit', 18, 2)->nullable();
            $table->decimal('eps', 10, 4)->nullable();
            $table->decimal('book_value_per_share', 10, 4)->nullable();
            $table->decimal('roe', 6, 3)->nullable();
            $table->decimal('roa', 6, 3)->nullable();
            $table->decimal('debt_equity', 6, 3)->nullable();
            $table->decimal('current_ratio', 6, 3)->nullable();
            $table->decimal('dividend_per_share', 10, 4)->nullable();
            $table->timestamp('ingested_at')->nullable();

            $table->unique(['company_id', 'period_type', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fundamental_data');
    }
};
