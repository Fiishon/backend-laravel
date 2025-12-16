<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresoUsuario extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subtema_id',
        'completado',
        'completed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subtema()
    {
        return $this->belongsTo(Subtema::class);
    }
}
