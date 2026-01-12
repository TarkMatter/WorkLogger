<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_report_status_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_report_id')
                ->constrained('daily_reports')
                ->cascadeOnDelete();

            $table->foreignId('actor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // created / submitted / approved / rejected など
            $table->string('action', 30);

            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30)->nullable();

            // rejected のときに理由を入れる想定（他も必要なら入れてOK）
            $table->text('reason')->nullable();

            // 予備（将来拡張用：IPやUAなど）
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['daily_report_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_status_logs');
    }
};
