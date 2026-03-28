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
        Schema::create('marketing_contact_details', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('phone_href');
            $table->string('email');
            $table->text('opening_hours')->nullable();
            $table->string('address_line')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('marketing_contact_details')->insert([
            'phone' => config('marketing.contact.phone'),
            'phone_href' => config('marketing.contact.phone_href'),
            'email' => config('marketing.contact.email'),
            'opening_hours' => 'Mandag–fredag 9–17 · Lørdag 9–13 · Søndag lukket. Køretimer kan bookes uden for kontortid via portalen.',
            'address_line' => 'Køregade 123, København',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_contact_details');
    }
};
