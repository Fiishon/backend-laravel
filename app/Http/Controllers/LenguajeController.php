<?php

namespace App\Http\Controllers;

use App\Models\Lenguaje;
use Illuminate\Http\Request;

class LenguajeController extends Controller
{
    public function index()
    {
        $lenguajes = Lenguaje::all();
        // Devolvemos JSON directo, sin wrappers 'data'
        return response()->json($lenguajes);
    }

    public function show($id)
    {
        $lenguaje = Lenguaje::find($id);
        if (!$lenguaje) {
            return response()->json(['message' => 'Lenguaje no encontrado'], 404);
        }
        return response()->json($lenguaje);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'color_hex' => 'nullable|string|max:7', // Ejemplo: #FFFFFF
        ]);

        $lenguaje = Lenguaje::create($validated);
        return response()->json($lenguaje, 201);
    }

    public function update(Request $request, $id)
    {
        $lenguaje = Lenguaje::find($id);
        if (!$lenguaje) {
            return response()->json(['message' => 'No encontrado'], 404);
        }
        
        $lenguaje->update($request->all());
        return response()->json($lenguaje);
    }

    public function destroy($id)
    {
        $lenguaje = Lenguaje::find($id);
        if (!$lenguaje) {
            return response()->json(['message' => 'No encontrado'], 404);
        }
        $lenguaje->delete();
        return response()->json(['message' => 'Eliminado correctamente']);
    }
}