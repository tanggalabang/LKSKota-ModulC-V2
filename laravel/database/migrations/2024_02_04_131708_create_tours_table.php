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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('destination_id')->nullable();
            $table->string('name');
            $table->text('description');
            $table->text('itinerary_sugesstion');
            $table->text('amenities_facilities');
            $table->string('maps');
            
            $table->foreign('destination_id')->references('id')->on('destinations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
