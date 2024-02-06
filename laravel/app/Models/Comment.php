<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = "comments";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'author_id',
        'type',
        'tour_id',
        'blog_local_experience_id',
        'content',
        'created_date',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function blogLocalExperience()
    {
        return $this->belongsTo(blogLocalExperience::class);
    }
}
