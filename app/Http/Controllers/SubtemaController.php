<?php

namespace App\Http\Controllers;

use App\Models\Subtema;
use App\Models\Unidad;
use Illuminate\Http\Request;

class SubtemaController extends Controller
{
    public function index($unidad_id)
    {
        $unidad = Unidad::findOrFail($unidad_id);
        return response()->json($unidad->subtemas()->orderBy('orden')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unidad_id' => 'required|exists:unidades,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'orden' => 'integer',
        ]);

        $subtema = Subtema::create($validated);
        return response()->json(['mensaje' => 'Subtema creado', 'datos' => $subtema], 201);
    }

    // --- ESTE ES EL MÉTODO CLAVE ---
    public function show($id)
    {
        // Traemos 'teoria' y 'ejercicios' para que el frontend tenga todo
        $subtema = Subtema::with(['teoria', 'ejercicios'])->find($id);

        if (!$subtema) {
            return response()->json(['mensaje' => 'Subtema no encontrado'], 404);
        }
        return response()->json($subtema);
    }
    
    public function update(Request $request, $id)
    {
        $subtema = Subtema::find($id);
        if (!$subtema) return response()->json(['mensaje' => 'No encontrado'], 404);
        
        $subtema->update($request->all());
        return response()->json(['mensaje' => 'Actualizado', 'datos' => $subtema]);
    }

    public function destroy($id)
    {
        $subtema = Subtema::find($id);
        if (!$subtema) return response()->json(['mensaje' => 'No encontrado'], 404);
        
        $subtema->delete();
        return response()->json(['mensaje' => 'Eliminado']);
    }
}

    
