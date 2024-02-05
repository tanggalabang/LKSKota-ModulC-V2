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
        Schema::create('checkout_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checkout_id')->nullable();
            $table->enum('payment_method', ['debit', 'credit', 'e-wallet']);
            $table->string('name_of_card');
            $table->string('number_of_card');
            $table->string('expiry_date');
            $table->string('cvv');

            $table->foreign('checkout_id')->references('id')->on('checkouts')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_payments');
    }
};
