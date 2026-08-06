<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // 'Bursa Malaysia'
            $table->string('sub_market', 20); // 'Main', 'ACE', 'LEAP'
            $table->timestamps();

            $table->unique(['name', 'sub_market']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
