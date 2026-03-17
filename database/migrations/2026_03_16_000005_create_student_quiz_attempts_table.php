<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offer_page_id')->constrained()->cascadeOnDelete();
            $table->json('answers');
            $table->tinyInteger('score');
            $table->tinyInteger('total');
            $table->timestamp('attempted_at');
            $table->timestamps();

            $table->index(['student_id', 'offer_page_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_quiz_attempts');
    }
};
