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

    // Hubungan invers ke Destination
    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    // Hubungan ke TourPicture
    public function tourPictures()
    {
        return $this->hasMany(TourPicture::class);
    }

    // Hubungan ke ReviewRating
    public function reviewRatings()
    {
        return $this->hasMany(ReviewRating::class);
    }

    // Hubungan ke ReviewRating
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
