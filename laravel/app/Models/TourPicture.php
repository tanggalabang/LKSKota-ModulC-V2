<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPicture extends Model
{
    use HasFactory;

    protected $table = "tour_pictures";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = false; 
    public $incrementing = true;

    protected $fillable = [
        'tour_id',
        'picture',
        'main'
    ];
}
