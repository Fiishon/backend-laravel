<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IAService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model;

public function __construct()
{
    $this->apiKey = env('GEMINI_API_KEY');
    // IMPORTANTE: Usar v1beta
    $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
    // Si no encuentra la variable en .env, usa el modelo 2.5 por defecto
    $this->model = env('GEMINI_MODEL', 'gemini-2.5-flash');
}

    public function preguntar($systemPrompt, $userPrompt)
{
    if (empty($this->apiKey)) {
        Log::error('IA Error: GEMINI_API_KEY no encontrada.');
        return null;
    }

    try {
        $url = "{$this->baseUrl}{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::withOptions(['verify' => false])
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'systemInstruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents' => [['role' => 'user', 'parts' => [['text' => $userPrompt]]]],
                'generationConfig' => [
                    'temperature' => 0.5,
                    'responseMimeType' => 'application/json'
                ]
            ]);

        // 1. Si la petición HTTP falla (400, 404, 500)
        if ($response->failed()) {
            Log::error('IA Error Google HTTP: ' . $response->status() . ' - ' . $response->body());
            return null;
        }

        $data = $response->json();

        // 2. Verificar si hay respuesta válida
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }

        // 3. Verificar bloqueos por seguridad (Safety Filters)
        if (isset($data['promptFeedback']['blockReason'])) {
            Log::error('IA Bloqueo de Seguridad: ' . $data['promptFeedback']['blockReason']);
            return json_encode([
                'es_correcto' => false,
                'retroalimentacion' => 'La IA no pudo evaluar el código por motivos de seguridad o contenido inapropiado.'
            ]); // Retornamos un JSON fallback para que no rompa el frontend
        }

        Log::error('IA Error: Estructura desconocida: ' . json_encode($data));
        return null;

    } catch (\Exception $e) {
        Log::error('IA Exception: ' . $e->getMessage());
        return null;
    }
}
}