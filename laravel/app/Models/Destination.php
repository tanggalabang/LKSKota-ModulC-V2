<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $table = "destinations";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'name',
        'picture'
    ];

    // Hubungan one-to-many dengan Tour
    public function tours()
    {
        return $this->hasMany(Tour::class, 'destination_id');
    }
}
