<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Lista todos los empleados con su rol.
     *
     * GET /api/users
     */
    public function index()
    {
        $users = User::with('role')->paginate(15);

        return response()->json($users);
    }

    /**
     * Retorna la lista de roles disponibles (para selects).
     *
     * GET /api/users/roles
     */
    public function roles()
    {
        return response()->json([
            'data' => Role::all(),
        ]);
    }

    /**
     * Crea un nuevo empleado.
     *
     * POST /api/users
     * Body: { name, email, password, password_confirmation, role_id }
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return response()->json([
            'message' => 'Empleado creado y asignado a su departamento.',
            'data'    => $user->load('role'),
        ], 201);
    }

    /**
     * Muestra un empleado específico.
     *
     * GET /api/users/{id}
     */
    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);

        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Actualiza los datos de un empleado.
     *
     * PUT /api/users/{id}
     * Body: { name, email, password?, password_confirmation?, role_id }
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Datos del empleado actualizados.',
            'data'    => $user->load('role'),
        ]);
    }

    /**
     * Elimina un empleado.
     *
     * DELETE /api/users/{id}
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Protección: no permitir auto-eliminación
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'No puedes eliminar tu propia cuenta administrativa.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'El usuario ha sido removido del sistema.',
        ]);
    }
}
