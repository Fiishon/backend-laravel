<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/crear-admin-secreto', function () {
    // 1. Verificar si ya existe
    if (User::where('correo', 'admin@expocode.com')->exists()) {
        return "El usuario Admin YA existe.";
    }

    // 2. Crear usando TUS columnas
    $user = User::create([
        'nombre'           => 'Super',
        'apellido_paterno' => 'Admin',
        'apellido_materno' => 'Sistema',
        'correo'           => 'admin@expocode.com',
        'contraseña'       => Hash::make('password123'), // Encriptamos la contraseña
        'rol'              => 'admin', // Asegúrate que 'admin' sea el valor correcto en tu DB
    ]);

    return "¡Admin creado con éxito! Correo: " . $user->correo;
});
