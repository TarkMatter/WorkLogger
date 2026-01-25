<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();

            // 例: projects.create / projects.update / projects.delete
            $table->string('key')->unique();

            // 表示用
            $table->string('label')->nullable();

            // グルーピング（projects / clients / ...）
            $table->string('group')->nullable();

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
