<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->boolean('requires_theory_exam')->default(true);
            $table->boolean('requires_practical_exam')->default(true);
        });

        DB::table('bookings')->where('type', 'exam')->update(['type' => 'theory_exam']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('bookings')->where('type', 'theory_exam')->update(['type' => 'exam']);

        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['requires_theory_exam', 'requires_practical_exam']);
        });
    }
};
