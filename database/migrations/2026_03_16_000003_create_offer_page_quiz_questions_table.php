<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_page_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_page_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->json('options');
            $table->tinyInteger('correct_option');
            $table->text('explanation')->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_page_quiz_questions');
    }
};
