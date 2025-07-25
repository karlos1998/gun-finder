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
        Schema::table('listings', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('condition')->nullable();
            $table->json('gallery_images')->nullable();
            $table->date('listing_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number',
                'city',
                'region',
                'condition',
                'gallery_images',
                'listing_date',
            ]);
        });
    }
};
