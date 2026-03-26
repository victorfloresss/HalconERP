<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Role; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Muestra la lista de empleados con sus roles cargados.
     */
    public function index()
    {
        // Usamos with('role') para evitar el problema de N+1 consultas
        // paginate(10) es ideal por si el equipo de Halcón crece mucho
        $users = User::with('role')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Envía la lista de roles al formulario de creación.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Guarda al nuevo empleado con su respectivo rol.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id', 
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index')->with('success', 'Empleado creado y asignado a su departamento.');
    }

    /**
     * Redirección simple para evitar errores si alguien entra a /users/{id}
     */
    public function show(User $user)
    {
        return redirect()->route('users.index');
    }

    /**
     * Envía al usuario y los roles al formulario de edición.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Actualiza los datos y el rol del empleado.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed', // Solo si decide cambiarla
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        
        // Solo actualiza la contraseña si el campo no está vacío
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->route('users.index')->with('success', 'Datos del empleado actualizados.');
    }

    /**
     * Elimina al usuario (evitando el suicidio administrativo).
     */
    public function destroy(User $user)
    {
        // Protección extra: No dejar que el admin logueado se borre
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Operación cancelada: No puedes eliminar tu propia cuenta administrativa.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'El usuario ha sido removido del sistema.');
    }
}