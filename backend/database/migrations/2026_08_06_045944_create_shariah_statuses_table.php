<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shariah_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_id')->constrained('securities');
            $table->string('status', 20); // 'compliant', 'non_compliant'
            $table->date('source_publication_date');
            $table->timestamp('imported_at');
            $table->foreignId('imported_by_user_id')->constrained('users');

            $table->index(['security_id', 'source_publication_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shariah_statuses');
    }
};
