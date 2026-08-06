<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_rule_id')->constrained('alert_rules')->cascadeOnDelete();
            $table->date('trigger_date'); // trading day the condition was evaluated for; prevents duplicate same-day triggers
            $table->text('message');
            $table->timestamp('notified_at')->nullable(); // set once included in a sent digest email
            $table->timestamp('read_at')->nullable();

            $table->unique(['alert_rule_id', 'trigger_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_triggers');
    }
};
