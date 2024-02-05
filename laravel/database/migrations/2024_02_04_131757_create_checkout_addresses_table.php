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
        Schema::create('checkout_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checkout_id')->nullable();
            $table->string('address_1');
            $table->string('address_2')->nullable();
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->string('country');

            $table->foreign('checkout_id')->references('id')->on('checkouts')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_addresses');
    }
};
