<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogLocalExperience extends Model
{
    use HasFactory;

    protected $table = "blog_local_experiences";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'author_id',
        'title',
        'picture',
        'content',
        'tour_id',
        'created_date',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id'); // Ganti 'author_id' dengan nama foreign key yang sesuai di tabel Anda
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
