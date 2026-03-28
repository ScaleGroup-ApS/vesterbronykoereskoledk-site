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
        Schema::create('marketing_home_copies', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('hero_headline_prefix');
            $table->string('hero_headline_accent');
            $table->text('hero_subtitle')->nullable();
            $table->string('why_title');
            $table->text('why_lead')->nullable();
            $table->string('reviews_title');
            $table->text('reviews_lead')->nullable();
            $table->string('reviews_footnote')->nullable();
            $table->string('explore_title');
            $table->text('explore_lead')->nullable();
            $table->string('cta_title');
            $table->text('cta_lead')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('marketing_home_copies')->insert([
            'key' => 'home',
            'hero_headline_prefix' => 'Kørekort med',
            'hero_headline_accent' => 'ro i maven',
            'hero_subtitle' => 'Vi underviser til rigtig trafik — ikke kun til prøven. Gennemse pakker og FAQ, eller skriv en linje, hvis du er i tvivl om et eller andet.',
            'why_title' => 'Hvorfor vælge os',
            'why_lead' => 'Kort fortalt: struktur, ærlig kommunikation og undervisning, der ligner den virkelighed, du møder ude på vejen.',
            'reviews_title' => 'Hvad siger eleverne',
            'reviews_lead' => 'Her er udvalgte udtalelser fra elever — rediger tekster og tilføj flere under Marketing i kontrolpanelet.',
            'reviews_footnote' => 'Citater kan være anonymiserede eller fra offentlige anmeldelser, afhængigt af hvad I vælger at vise.',
            'explore_title' => 'Find det du leder efter',
            'explore_lead' => 'Siderne er delt op, så du ikke skal scrolle evigt for at finde priser eller kontaktinfo.',
            'cta_title' => 'Klar til at tale om dit forløb?',
            'cta_lead' => 'Vi kan ikke love en dato på prøven herfra — men vi kan love, at du får et svar, der giver mening, hvis du rækker ud.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_home_copies');
    }
};
