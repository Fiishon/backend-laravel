<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'correo' => 'required|string|email|max:255|unique:users',
            'contraseña' => 'required|string|min:8',
            'rol' => 'required|string|in:' . User::ROL_ADMIN . ',' . User::ROL_ESTUDIANTE,
        ]);

        $user = User::create([
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'correo' => $request->correo,
            'contraseña' => Hash::make($request->contraseña),
            'rol' => $request->rol,
        ]);
        
        return response()->json(([
            'mensaje' => 'Usuario creado exitosamente',
            'user' => $user,
        ]), 201);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        $user->delete();

        return response()->json(['mensaje' => 'Usuario eliminado exitosamente'], 200);
    }

    public function foto(Request $requeest)
    {
        $request->validate([
            'foto_perfil' => 'nullable|image|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('foto_perfil')) {

            if ($user->foto_perfil) {
                $oldPath = str_replace(asset('storage/'), '', $user->foto_perfil);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('foto_perfil')->store('perfiles', 'public');

            $url = asset('storage/' . $path);

            $user->update(['foto_perfil' => $url]);

            return response()->json([
                'mensaje' => 'Foto de perfil actualizada exitosamente',
                'foto_perfil' => $url,
            ], 200);
        }
        return response()->json(['mensaje' => 'No se proporcionó una foto de perfil'], 400);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        // Validación para actualización
        $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'apellido_paterno' => 'sometimes|required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'correo' => [
                'sometimes', 'required', 'email', 'max:255',
                // Ignora el ID actual para permitir guardar sin cambiar el correo
                Rule::unique('users', 'correo')->ignore($user->id) 
            ],
            'rol' => 'sometimes|required|string|in:admin,estudiante',
            'contraseña' => 'nullable|string|min:8', // Opcional
        ]);

        // Actualización condicional de campos
        if ($request->has('nombre')) $user->nombre = $request->nombre;
        if ($request->has('apellido_paterno')) $user->apellido_paterno = $request->apellido_paterno;
        if ($request->has('apellido_materno')) $user->apellido_materno = $request->apellido_materno;
        if ($request->has('correo')) $user->correo = $request->correo;
        if ($request->has('rol')) $user->rol = $request->rol;

        // Solo actualizar contraseña si viene en la petición y no está vacía
        if ($request->filled('contraseña')) {
            $user->contraseña = Hash::make($request->contraseña);
        }

        $user->save();

        return response()->json([
            'mensaje' => 'Usuario actualizado exitosamente',
            'user' => $user,
        ], 200);
    }
}
