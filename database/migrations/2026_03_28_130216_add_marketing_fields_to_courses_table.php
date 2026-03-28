<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('featured_on_home')->default(false)->after('max_students');
            $table->unsignedInteger('public_spots_remaining')->nullable()->after('featured_on_home');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['featured_on_home', 'public_spots_remaining']);
        });
    }
};
