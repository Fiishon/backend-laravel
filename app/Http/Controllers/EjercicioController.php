<?php

namespace App\Http\Controllers;

use App\Models\Ejercicio;
use Illuminate\Http\Request;

class EjercicioController extends Controller
{
    public function index()
    {
        return response()->json(Ejercicio::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'subtema_id' => 'required|exists:subtemas,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'dificultad' => 'nullable|string',
            'starter_code' => 'nullable|string',
            'solucion' => 'nullable|string',
        ]);

        $ejercicio = Ejercicio::create($request->all());
        
        return response()->json(([
            'mensaje' => 'Ejercicio creado exitosamente',
            'datos' => $ejercicio,
        ]), 201);
    }

    public function show($id)
    {
        $ejercicio = Ejercicio::find($id);
        if (!$ejercicio) {
            return response()->json(['mensaje' => 'Ejercicio no encontrado'], 404);
        }
        return response()->json($ejercicio);
    }

    public function update(Request $request, $id)
    {
        $ejercicio = Ejercicio::find($id);

        if (!$ejercicio) {
            return response()->json(['mensaje' => 'Ejercicio no encontrado'], 404);
        }

        $ejercicio->update($request->all());

        return response()->json(([
            'mensaje' => 'Ejercicio actualizado exitosamente',
            'datos' => $ejercicio,
        ]), 200);
    }

    public function destroy($id)
    {
        $ejercicio = Ejercicio::find($id);
        if ($ejercicio) {
            $ejercicio->delete();
        }
        
        return response()->json(['mensaje' => 'Ejercicio eliminado exitosamente'], 200);
    }
}
