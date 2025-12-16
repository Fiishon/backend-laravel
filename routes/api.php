<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\SubtemaController;
use App\Http\Controllers\TeoriaController;
use App\Http\Controllers\EjercicioController;
use App\Http\Controllers\ProgresoUsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IAController;
use App\Http\Controllers\LenguajeController;
use App\Http\Controllers\AuthSocialController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/auth/google', [AuthSocialController::class, 'LoginGoogle']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/user/foto', [UserController::class, 'foto']);

    Route::get('lenguajes', [LenguajeController::class, 'index']);
    Route::get('lenguajes/{id}', [LenguajeController::class, 'show']);
    
    Route::apiResource('materias', MateriaController::class);
    Route::get('materias/{id}/unidades', [UnidadController::class, 'index']);

    Route::apiResource('unidades', UnidadController::class)->except(['index']);
    Route::get('unidades/{id}/subtemas', [SubtemaController::class, 'index']);

    // Subtemas ahora devuelve también la teoría y ejercicios en el método show
    Route::apiResource('subtemas', SubtemaController::class)->except(['index']);

    Route::apiResource('teorias', TeoriaController::class);
    Route::apiResource('ejercicios', EjercicioController::class);


    Route::get('/progreso', [ProgresoUsController::class, 'index']);
    Route::post('/progreso', [ProgresoUsController::class, 'store']);

    // Rutas de Inteligencia Artificial
    Route::post('/ia/teoria', [IAController::class, 'explicar']); // Generar teoría (opcional si usas DB)
    Route::post('/ia/quiz', [IAController::class, 'generarQuiz']); // NUEVO: Generar quiz
    Route::post('/ia/evaluar', [IAController::class, 'evaluar']); // Evaluar código final

    Route::middleware('admin')->group(function () {
        
        Route::apiResource('users', UserController::class);


        
        Route::post('lenguajes', [LenguajeController::class, 'store']);
        Route::put('lenguajes/{id}', [LenguajeController::class, 'update']);
        Route::delete('lenguajes/{id}', [LenguajeController::class, 'destroy']);

        Route::post('materias', [MateriaController::class, 'store']);
        Route::put('materias/{id}', [MateriaController::class, 'update']);
        Route::delete('materias/{id}', [MateriaController::class, 'destroy']);

        Route::post('unidades', [UnidadController::class, 'store']);
        Route::put('unidades/{id}', [UnidadController::class, 'update']);
        Route::delete('unidades/{id}', [UnidadController::class, 'destroy']);

        Route::post('subtemas', [SubtemaController::class, 'store']);
        Route::put('subtemas/{id}', [SubtemaController::class, 'update']);
        Route::delete('subtemas/{id}', [SubtemaController::class, 'destroy']);

        Route::post('teorias', [TeoriaController::class, 'store']);
        Route::put('teorias/{id}', [TeoriaController::class, 'update']);
        Route::delete('teorias/{id}', [TeoriaController::class, 'destroy']);

        Route::post('ejercicios', [EjercicioController::class, 'store']);
        Route::put('ejercicios/{id}', [EjercicioController::class, 'update']);
        Route::delete('ejercicios/{id}', [EjercicioController::class, 'destroy']);
    });
});