<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use App\Models\Materia;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function index($materia_id)
    {
        $materia = Materia::find($materia_id);
        if (!$materia) {
            return response()->json(['mensaje' => 'Materia no encontrada'], 404);
        }
        return response()->json($materia->unidades);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'nombre' => 'required|string|max:255',
            'orden' => 'integer',
        ]);

        $unidad = Unidad::create($validated);
        
        return response()->json(([
            'mensaje' => 'Unidad creada exitosamente',
            'datos' => $unidad,
        ]), 201);
    }

    public function show($id)
    {
        $unidad = Unidad::with('subtemas')->find($id);
        if (!$unidad) {
            return response()->json(['mensaje' => 'Unidad no encontrada'], 404);
        }
        return response()->json($unidad);
    }

    public function update(Request $request, $id)
    {
        $unidad = Unidad::find($id);

        if (!$unidad) {
            return response()->json(['mensaje' => 'Unidad no encontrada'], 404);
        }

        $unidad->update($request->all());

        return response()->json(([
            'mensaje' => 'Unidad actualizada exitosamente',
            'datos' => $unidad,
        ]), 200);
    }

    public function destroy($id)
    {
        $unidad = Unidad::find($id);

        if (!$unidad) {
            return response()->json(['mensaje' => 'Unidad no encontrada'], 404);
        }

        $unidad->delete();

        return response()->json(['mensaje' => 'Unidad eliminada exitosamente'], 200);
    }
}
