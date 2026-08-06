<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('securities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('ticker', 20)->unique();
            $table->string('type', 30)->default('ordinary_shares');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('securities');
    }
};
