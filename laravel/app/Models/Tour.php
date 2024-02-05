<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    
    protected $table = "tours";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = false; 
    public $incrementing = true;

    protected $fillable = [
        'destination_id',
        'name',
        'description',
        'itinerary_sugesstion',
        'amenities_facilities',
        'maps'
    ];
}
