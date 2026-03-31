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
        Schema::create('marketing_testimonials', function (Blueprint $table) {
            $table->id();
            $table->text('quote');
            $table->string('author_name');
            $table->string('author_detail')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        $rows = [
            [
                'quote' => 'Jeg skubbede teorien foran mig længe. Her fik jeg lov at komme tilbage og repitere uden at føle mig til besvær. Roen før prøven betød meget.',
                'author_name' => 'Sofie',
                'author_detail' => 'Kørekort B',
                'sort_order' => 1,
            ],
            [
                'quote' => 'Jeg gad ikke gætte på, hvad der var inkluderet. Pakken var som beskrevet — og da jeg spurgte ind til ekstra kørsel, fik jeg et klart svar samme dag.',
                'author_name' => 'Marcus',
                'author_detail' => 'Pakke med lovpligtige timer',
                'sort_order' => 2,
            ],
            [
                'quote' => 'Min kørelærer sagde både hvad jeg var god til, og hvad der haltede. Ingen plattener. Bestod første gang — lettelsen var ægte.',
                'author_name' => 'Jonas',
                'author_detail' => 'Kørekort B',
                'sort_order' => 3,
            ],
        ];
        foreach ($rows as $row) {
            DB::table('marketing_testimonials')->insert(array_merge($row, [
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
        Schema::dropIfExists('marketing_testimonials');
    }
};
