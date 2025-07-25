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
            // Drop the existing unique constraint on listing_id
            $table->dropUnique('listings_listing_id_unique');

            // Add a composite unique constraint on listing_id and provider
            $table->unique(['listing_id', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique(['listing_id', 'provider']);

            // Add back the unique constraint on listing_id
            $table->unique('listing_id');
        });
    }
};
