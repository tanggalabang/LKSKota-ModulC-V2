<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewRating extends Model
{
    use HasFactory;

    protected $table = "review_ratings";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = false; 
    public $incrementing = true;

    protected $fillable = [
        'author_id',
        'tour_id',
        'rating',
        'content',
        'created_date',
    ];
}
