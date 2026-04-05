<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('theory_practice_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('score')->unsigned();
            $table->tinyInteger('total')->unsigned();
            $table->unsignedSmallInteger('duration_seconds');
            $table->json('answers');
            $table->json('question_ids');
            $table->timestamp('attempted_at');

            $table->index(['student_id', 'attempted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theory_practice_attempts');
    }
};
