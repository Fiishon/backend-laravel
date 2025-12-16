<?php

namespace App\Http\Controllers;

use App\Models\ProgresoUsuario;
use App\Models\Subtema;
use Illuminate\Http\Request;

class ProgresoUsController extends Controller
{
    public function store(Request $request)
    {
        // Validamos que venga el subtema_id
        $validated = $request->validate([
            'subtema_id' => 'required|integer|exists:subtemas,id',
        ]);

        // Obtenemos el usuario autenticado (gracias a Sanctum)
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        // Usamos updateOrCreate para evitar duplicados
        $progreso = ProgresoUsuario::updateOrCreate(
            [
                'user_id' => $user->id, // Usamos el ID del usuario autenticado
                'subtema_id' => $validated['subtema_id'], // Usamos el dato validado
            ],
            [
                'completado' => true,
                'completed_at' => now() 
            ]
        );

        return response()->json([
            'mensaje' => 'Progreso guardado exitosamente',
            'datos' => $progreso,
        ], 201);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        // Obtenemos los IDs de los subtemas completados
        $subtemaCompletado = $user->progreso()
                                  ->where('completado', true)
                                  ->pluck('subtema_id');

        return response()->json([
            'subtemas_completados' => $subtemaCompletado,
        ]);
    }
}