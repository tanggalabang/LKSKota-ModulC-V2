<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutAddress extends Model
{
    use HasFactory;

    protected $table = "checkout_addresses";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'checkout_id',
        'address_1',
        'address_2',
        'city',
        'province',
        'postal_code',
        'country'
    ];
}
