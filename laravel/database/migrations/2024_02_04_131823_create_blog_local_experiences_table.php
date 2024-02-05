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
        Schema::create('blog_local_experiences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('title');
            $table->string('picture');
            $table->longText('content');
            $table->unsignedBigInteger('tour_id')->nullable();
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
        Schema::dropIfExists('blog_local_experiences');
    }
};
