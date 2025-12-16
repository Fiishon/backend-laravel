<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $fillable = [
        'lenguaje_id',
        'nombre',
        'descripcion',
        'imagen',
    ];

    public function lenguaje()
    {
        return $this->belongsTo(Lenguaje::class);
    }

    public function unidades()
    {
        return $this->hasMany(Unidad::class)->orderBy('orden');
    }
}
