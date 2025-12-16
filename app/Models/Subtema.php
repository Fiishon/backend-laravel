<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtema extends Model
{
    use HasFactory;

    protected $fillable = [
        'unidad_id',
        'nombre',
        'descripcion',
        'orden',
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function teoria()
    {
        return $this->hasOne(Teoria::class);
    }

    public function ejercicios()
    {
        return $this->hasMany(Ejercicio::class);
    }

    public function progreso()
    {
        return $this->hasOne(ProgesoUsuario::class)->where('user_id', auth()->id());
    }
}
