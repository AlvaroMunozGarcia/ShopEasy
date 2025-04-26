<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect; // Para redirecciones más limpias
use Illuminate\Validation\Rule; // Para reglas de validación avanzadas
use Spatie\Permission\Models\Role; // Importa el modelo Role
use Illuminate\Support\Facades\DB; // Para transacciones (opcional pero recomendado)
use Illuminate\Http\RedirectResponse; // Para type hinting

class UserController extends Controller
{
    /**
     * Muestra una lista de los usuarios.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener usuarios con sus roles cargados (evita N+1 queries)
        // Ordenar por ID descendente para ver los más nuevos primero
        $users = User::with('roles')->latest('id')->paginate(10); // Pagina cada 10 usuarios

        return view('admin.users.index', compact('users'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Obtener todos los nombres de roles disponibles
        $roles = Role::pluck('name', 'name')->all(); // Usar name como clave y valor

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación de los datos de entrada
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email', // Asegura email único
            'password' => 'required|string|min:8|confirmed', // 'confirmed' busca 'password_confirmation'
            'roles' => 'required|array', // Asegura que 'roles' sea un array
            'roles.*' => ['required', Rule::exists('roles', 'name')] // Valida que cada rol exista en la tabla 'roles' por nombre
        ]);

        // Iniciar transacción para asegurar atomicidad (opcional pero buena práctica)
        DB::beginTransaction();
        try {
            // Crear el usuario
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Asignar los roles seleccionados
            $user->assignRole($validatedData['roles']);

            DB::commit(); // Confirmar transacción

            return Redirect::route('admin.users.index')
                           ->with('success', 'Usuario creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir transacción en caso de error
            // Log::error("Error al crear usuario: " . $e->getMessage()); // Opcional: Loguear el error
            return Redirect::back()
                           ->with('error', 'Error al crear el usuario. Inténtelo de nuevo.')
                           ->withInput(); // Mantener los datos del formulario
        }
    }

    /**
     * Muestra el formulario para editar un usuario específico.
     * (Asume Route Model Binding: User $user)
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        // Obtener todos los nombres de roles disponibles
        $roles = Role::pluck('name', 'name')->all();

        // Cargar la vista de edición pasando el usuario y los roles
        // Necesitarás crear la vista 'admin.users.edit.blade.php'
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Actualiza el usuario especificado en la base de datos.
     * (Asume Route Model Binding: User $user)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Validación de los datos de entrada
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            // Validar email único, ignorando el usuario actual
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            // La contraseña es opcional en la actualización
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => ['required', Rule::exists('roles', 'name')]
        ]);

        // Iniciar transacción
        DB::beginTransaction();
        try {
            // Preparar datos para actualizar (excluyendo contraseña si está vacía)
            $updateData = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
            ];

            // Si se proporcionó una nueva contraseña, hashearla y añadirla
            if (!empty($validatedData['password'])) {
                $updateData['password'] = Hash::make($validatedData['password']);
            }

            // Actualizar los datos del usuario
            $user->update($updateData);

            // Sincronizar roles (elimina los antiguos y añade los nuevos seleccionados)
            $user->syncRoles($validatedData['roles']);

            DB::commit(); // Confirmar transacción

            return Redirect::route('admin.users.index')
                           ->with('success', 'Usuario actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir transacción
            // Log::error("Error al actualizar usuario {$user->id}: " . $e->getMessage()); // Opcional: Loguear
            return Redirect::back()
                           ->with('error', 'Error al actualizar el usuario. Inténtelo de nuevo.')
                           ->withInput();
        }
    }

    /**
     * Elimina el usuario especificado de la base de datos.
     * (Asume Route Model Binding: User $user)
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        // --- Medidas de Seguridad ---
        // 1. Prevenir que el usuario se elimine a sí mismo
        if (auth()->id() === $user->id) {
            return Redirect::route('admin.users.index')
                           ->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
        }

        // 2. Prevenir la eliminación de usuarios con el rol 'Admin' (o roles críticos)
        if ($user->hasRole('Admin')) {
             // Podrías hacer esto más flexible buscando un ID específico o una bandera 'is_protected'
            return Redirect::route('admin.users.index')
                           ->with('error', 'No se puede eliminar a un usuario con el rol de Administrador.');
        }

        // --- Eliminación ---
        try {
            $userName = $user->name; // Guardar nombre para el mensaje
            $user->delete();

            return Redirect::route('admin.users.index')
                           ->with('success', "Usuario '{$userName}' eliminado exitosamente.");

        } catch (\Exception $e) {
            // Log::error("Error al eliminar usuario {$user->id}: " . $e->getMessage()); // Opcional: Loguear
            // Podría haber restricciones de clave externa si el usuario está ligado a otras tablas
            return Redirect::route('admin.users.index')
                           ->with('error', 'Error al eliminar el usuario. Es posible que esté asociado a registros existentes (ventas, compras, etc.).');
        }
    }

     /**
      * Muestra los detalles de un usuario específico (Opcional).
      * Si no generaste el método show con --resource, puedes añadirlo.
      *
      * @param  \App\Models\User  $user
      * @return \Illuminate\View\View
      */
     // public function show(User $user)
     // {
     //     // Cargar roles si es necesario mostrarlos en detalle
     //     $user->load('roles');
     //     // Necesitarás crear la vista 'admin.users.show.blade.php'
     //     return view('admin.users.show', compact('user'));
     // }
}
