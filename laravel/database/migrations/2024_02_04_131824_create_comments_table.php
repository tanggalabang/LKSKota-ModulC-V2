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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('author_id');
            $table->enum('type', ['tour', 'blog']);
            $table->unsignedBigInteger('tour_id')->nullable();
            $table->unsignedBigInteger('blog_local_experience_id')->nullable();
            $table->text('content');
            $table->date('created_date');

            $table->foreign('author_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('tour_id')->references('id')->on('tours')->onDelete('set null');
            $table->foreign('blog_local_experience_id')->references('id')->on('blog_local_experiences')->onDelete('set null');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
