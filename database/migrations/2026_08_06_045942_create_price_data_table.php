<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_id')->constrained('securities');
            $table->date('trade_date');
            $table->decimal('open', 12, 4);
            $table->decimal('high', 12, 4);
            $table->decimal('low', 12, 4);
            $table->decimal('close', 12, 4);
            $table->unsignedBigInteger('volume')->default(0);
            $table->timestamp('ingested_at')->nullable();

            $table->unique(['security_id', 'trade_date']);
            $table->index('trade_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_data');
    }
};
