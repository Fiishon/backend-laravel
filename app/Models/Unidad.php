<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    protected $table = 'unidades'; 

    protected $fillable = [
        'materia_id',
        'nombre',
        'orden',
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function subtemas()
    {
        return $this->hasMany(Subtema::class)->orderBy('orden');
    }
}
