<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class Ejercicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'subtema_id',
        'dificultad',
        'titulo',
        'descripcion',
        'starter_code',
        'solucion',
    ];

    public function subtema()
    {
        return $this->belongsTo(Subtema::class);
    }
}
