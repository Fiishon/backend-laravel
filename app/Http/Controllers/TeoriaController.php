<?php

namespace App\Http\Controllers;

use App\Models\Teoria;
use App\Models\Subtema;
use Illuminate\Http\Request;

class TeoriaController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'subtema_id' => 'required|exists:subtemas,id',
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'video_url' => 'nullable|url',
        ]);

        $contenido = Teoria::updateOrCreate(
            [
                'subtema_id' => $request->subtema_id],
            [
                'titulo' => $request->titulo,
                'contenido' => $request->contenido,
                'video_url' => $request->video_url
            ]
        );
        
         return response()->json(([
            'mensaje' => 'Teoría creada/actualizada exitosamente',
            'datos' => $contenido,
        ]), 201);
    }

    public function show($id)
    {
        $contenido = Teoria::find($id);
        if (!$contenido) {
            return response()->json(['mensaje' => 'Teoría no encontrada'], 404);
        }
        return response()->json($contenido);
    }

    public function destroy($id)
    {
        $contenido = Teoria::find($id);
        if ($contenido) {
            $contenido->delete();
        }
        
        return response()->json(['mensaje' => 'Teoría eliminada exitosamente'], 200);
    }

     public function index()
    {
        return response()->json(Teoria::all());
    }

    // Agrega este para actualizar (PUT/PATCH)
    public function update(Request $request, $id)
    {
        $teoria = Teoria::find($id);
        if (!$teoria) return response()->json(['mensaje' => 'No encontrada'], 404);
        
        $teoria->update($request->all());
        return response()->json(['mensaje' => 'Actualizada', 'datos' => $teoria]);
    }

}
