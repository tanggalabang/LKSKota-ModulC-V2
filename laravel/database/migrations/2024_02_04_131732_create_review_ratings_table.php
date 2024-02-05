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
        Schema::create('review_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('tour_id')->nullable();
            $table->integer('rating');
            $table->string('content')->nullable();
            $table->date('created_date');

            $table->foreign('author_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('tour_id')->references('id')->on('tours')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_ratings');
    }
};
