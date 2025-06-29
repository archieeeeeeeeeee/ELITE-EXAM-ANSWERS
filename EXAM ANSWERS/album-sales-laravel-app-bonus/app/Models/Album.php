<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'name',
        'sales',
        'album_cover_path',
        'artist_id',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }
}
