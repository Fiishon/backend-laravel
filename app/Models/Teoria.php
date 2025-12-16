<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'subtema_id',
        'contenido',
        'video_url',
    ];

    public function subtema()
    {
        return $this->belongsTo(Subtema::class);
    }
}
