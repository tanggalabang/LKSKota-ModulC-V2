<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutPayment extends Model
{
    use HasFactory;

    protected $table = "checkout_payments";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'checkout_id',
        'payment_method',
        'name_of_card',
        'number_of_card',
        'expiry_date',
        'cvv'
    ];
}
