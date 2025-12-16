<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IAService;
use App\Models\Subtema;
use App\Models\Ejercicio;

class IAController extends Controller
{
    protected $ia;

    public function __construct(IAService $iaService)
    {
        $this->ia = $iaService;
    }

    // Genera preguntas de opción múltiple basadas en el tema
    public function generarQuiz(Request $request)
    {
        $request->validate([
            'subtema_id' => 'required|exists:subtemas,id',
            'cantidad' => 'integer|min:1|max:5',
        ]);

        $subtema = Subtema::with('unidad.materia')->find($request->subtema_id);
        $cantidad = $request->input('cantidad', 10);
        
        // Contexto para la IA
        $contexto = "Materia: " . ($subtema->unidad->materia->nombre ?? 'Programación') . 
                    ". Tema: " . $subtema->nombre . 
                    ". Descripción: " . $subtema->descripcion;

        $systemPrompt = "Eres un profesor que crea exámenes. Responde ÚNICAMENTE con JSON válido.";
        
        $userPrompt = "Genera un quiz de {$cantidad} preguntas sobre: {$contexto}. \n" .
                      "Estructura JSON exacta requerida (sin markdown):\n" .
                      "{ \n" .
                      "  \"quiz\": [ \n" .
                      "    { \n" .
                      "      \"pregunta\": \"Texto de la pregunta\", \n" .
                      "      \"opciones\": [\"A) ...\", \"B) ...\", \"C) ...\", \"D) ...\"], \n" .
                      "      \"respuesta_correcta_index\": 0, \n" . // 0=A, 1=B, etc.
                      "      \"retroalimentacion\": \"Explicación breve.\" \n" .
                      "    } \n" .
                      "  ] \n" .
                      "}";

        $jsonResponse = $this->ia->preguntar($systemPrompt, $userPrompt);
        return $this->procesarRespuestaJson($jsonResponse);
    }

    // Evalúa el código del ejercicio final
    public function evaluar(Request $request)
    {
        $request->validate([
            'ejercicio_id' => 'required|exists:ejercicios,id',
            'codigo_usuario' => 'required|string',
        ]);

        $ejercicio = Ejercicio::findOrFail($request->ejercicio_id);

        $systemPrompt = "Eres un tutor de programación estricto pero útil. Responde solo con JSON.";
        
        $userPrompt = "Ejercicio: {$ejercicio->titulo}. \n" .
                      "Consigna: {$ejercicio->descripcion} \n\n" .
                      "Código del alumno: \n```\n{$request->codigo_usuario}\n```\n\n" .
                      "Responde JSON: { \"es_correcto\": boolean, \"retroalimentacion\": \"Feedback constructivo.\" }";
        
        $jsonResponse = $this->ia->preguntar($systemPrompt, $userPrompt);
        return $this->procesarRespuestaJson($jsonResponse);
    }

    private function procesarRespuestaJson($rawResponse)
    {
        if (!$rawResponse) {
            return response()->json(['error' => 'Error de conexión con IA'], 503);
        }
        // Limpiamos markdown por si acaso la IA lo incluye
        $cleanJson = str_replace(['```json', '```'], '', $rawResponse);
        $decoded = json_decode($cleanJson);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Respuesta inválida de IA'], 500);
        }
        return response()->json($decoded);
    }
}