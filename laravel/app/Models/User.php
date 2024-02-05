<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = "users";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = false; 
    public $incrementing = true;

    protected $fillable = [
        'email',
        'password',
        'name',
        'picture'
    ];

    protected $hidden = [
        'password'
    ];
}
