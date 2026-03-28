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
        Schema::create('marketing_value_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('icon')->default('book_open');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        $rows = [
            [
                'title' => 'Klasseundervisning i teorien',
                'body' => 'Vi tror på samtale og spørgsmål i lokalet — ikke kun skærm derhjemme. Det er der, vanerne i trafikken begynder.',
                'icon' => 'book_open',
                'sort_order' => 1,
            ],
            [
                'title' => 'Overblik i hverdagen',
                'body' => 'Du skal kunne se planen for dit forløb uden at lede i tre apps. Elevportalen samler det, der er relevant for dig.',
                'icon' => 'users',
                'sort_order' => 2,
            ],
            [
                'title' => 'Kørelærere, du møder igen',
                'body' => 'Vi arbejder i teams, så du ikke starter forfra med en ny stemme hver anden uge — medmindre du selv ønsker skifte.',
                'icon' => 'car',
                'sort_order' => 3,
            ],
            [
                'title' => 'Tydelige pakker',
                'body' => 'Hvad der er med i prisen, står på pakkesiden. Er du i tvivl, så spørg — vi hellere en ekstra mail end en misforståelse.',
                'icon' => 'package',
                'sort_order' => 4,
            ],
        ];
        foreach ($rows as $row) {
            DB::table('marketing_value_blocks')->insert(array_merge($row, [
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_value_blocks');
    }
};
