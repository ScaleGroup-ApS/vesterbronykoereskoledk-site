<?php

use App\Models\Offer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        Offer::query()->each(function (Offer $offer) {
            $base = Str::slug($offer->name);
            $slug = $base !== '' ? $base.'-'.$offer->id : 'pakke-'.$offer->id;
            $offer->forceFill(['slug' => $slug])->saveQuietly();
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
