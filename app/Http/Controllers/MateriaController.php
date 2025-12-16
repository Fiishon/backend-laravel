<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;
use App\Models\ProgresoUsuario;

class MateriaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtenemos el usuario autenticado
        $user = $request->user();

        // 2. Traemos todas las materias con sus subtemas para contar
        $materias = Materia::with('unidades.subtemas')->get();

        // 3. Obtenemos los IDs de subtemas que este usuario ya completó
        // Nota: Usamos la clase directa para evitar errores de tipeo en el modelo User
        $subtemasCompletadosIds = ProgresoUsuario::where('user_id', $user->id)
            ->where('completado', true)
            ->pluck('subtema_id')
            ->toArray();

        // Variable para controlar la cadena: La primera materia siempre está habilitada
        $anteriorCompletada = true; 

        // 4. Transformamos la colección para agregar datos de progreso y bloqueo
        $materiasProcesadas = $materias->map(function ($materia) use ($subtemasCompletadosIds, &$anteriorCompletada) {
            
            // Aplanamos todos los subtemas de la materia para contarlos
            $subtemasDeMateria = $materia->unidades->flatMap(fn($u) => $u->subtemas)->pluck('id')->toArray();
            
            $totalSubtemas = count($subtemasDeMateria);
            
            // Cuántos de esos subtemas ha hecho el usuario
            $completadosCount = count(array_intersect($subtemasDeMateria, $subtemasCompletadosIds));

            // Cálculo de porcentaje (evitamos división por cero)
            $porcentaje = $totalSubtemas > 0 ? round(($completadosCount / $totalSubtemas) * 100) : 0;
            
            // Lógica de Bloqueo:
            // Está bloqueada si la ANTERIOR no se completó.
            $bloqueado = !$anteriorCompletada;

            // Actualizamos la bandera para la SIGUIENTE iteración:
            // ¿Esta materia se considera "pasada"? (Digamos que requiere 100%, puedes bajarlo a 80%)
            $estaMateriaAprobada = ($porcentaje >= 100); 
            
            // Si la materia actual no tiene subtemas (está vacía), no bloqueamos la siguiente cadena
            if ($totalSubtemas === 0) $estaMateriaAprobada = true;

            $anteriorCompletada = $estaMateriaAprobada;

            // Retornamos la estructura limpia para el Frontend
            return [
                'id' => $materia->id,
                'nombre' => $materia->nombre,
                'descripcion' => $materia->descripcion,
                'lenguaje_id' => $materia->lenguaje_id,
                'imagen' => $materia->imagen,
                'porcentaje' => $porcentaje,
                'bloqueado' => $bloqueado,
                // 'total_temas' => $totalSubtemas, // Útil para debug
            ];
        });

        return response()->json($materiasProcesadas);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|string',
        ]);

        $materia = Materia::create($request->all());
        
        return response()->json(([
            'mensaje' => 'Materia creada exitosamente',
            'materia' => $materia,
        ]), 201);
    }

    public function show($id)
    {
        $materia = Materia::with(['unidades.subtemas'])->find($id);
        if (!$materia) {
            return response()->json(['mensaje' => 'Materia no encontrada'], 404);
        }
        return response()->json($materia);
    }

    public function update(Request $request, $id)
    {
        $materia = Materia::find($id);

        if (!$materia) {
            return response()->json(['mensaje' => 'Materia no encontrada'], 404);
        }

        $materia->update($request->all());

        return response()->json(([
            'mensaje' => 'Materia actualizada exitosamente',
            'datos' => $materia,
        ]), 200);
    }

    public function destroy($id)
    {
        $materia = Materia::find($id);

        if (!$materia) {
            return response()->json(['mensaje' => 'Materia no encontrada'], 404);
        }

        $materia->delete();

        return response()->json(['mensaje' => 'Materia eliminada exitosamente'], 200);
    }
}
