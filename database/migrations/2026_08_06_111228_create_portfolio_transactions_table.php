<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained('portfolios')->cascadeOnDelete();
            $table->foreignId('security_id')->constrained('securities');
            $table->string('type', 10); // 'buy', 'sell'
            $table->decimal('quantity', 15, 4);
            $table->decimal('price', 12, 4); // price per share at transaction
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['portfolio_id', 'security_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_transactions');
    }
};
