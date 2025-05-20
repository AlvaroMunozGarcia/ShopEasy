<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Constructor para aplicar middleware.
     */
    public function __construct()
    {
        // Asegura que solo usuarios con el rol 'Admin' puedan acceder a estos métodos
        $this->middleware('role:Admin');
    }

    /**
     * Muestra un listado de todos los roles.
     * GET /admin/roles (asumiendo ruta similar a users)
     */
    public function index()
    {
        // Obtiene todos los roles y carga sus permisos asociados para mostrarlos en la vista
        $roles = Role::with('permissions')->orderBy('name')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Muestra los detalles de un rol específico, incluyendo sus permisos.
     * GET /admin/roles/{role} (asumiendo ruta similar a users)
     *
     * @param  \Spatie\Permission\Models\Role  $role El rol a mostrar (obtenido por Route Model Binding)
     */
    public function show(Role $role)
    {
        // Carga los permisos asociados a este rol específico
        $role->load('permissions');
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Muestra el formulario para crear un nuevo rol.
     * GET /admin/roles/create
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Almacena un nuevo rol en la base de datos.
     * POST /admin/roles
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id', // Valida que cada ID de permiso exista
        ]);

        $role = Role::create(['name' => $request->name]); // El guard_name se tomará por defecto

        if ($request->has('permissions')) {
            $permissionIds = $request->input('permissions');
            $permissions = Permission::whereIn('id', $permissionIds)->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('admin.roles.index')
                         ->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un rol existente.
     * GET /admin/roles/{role}/edit
     *
     * @param  \Spatie\Permission\Models\Role  $role
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Actualiza un rol existente en la base de datos.
     * PUT/PATCH /admin/roles/{role}
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Spatie\Permission\Models\Role  $role
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->name = $request->name;
        $role->save();
        if ($request->has('permissions')) {
            $permissionModels = Permission::whereIn('id', $request->input('permissions'))->get();
            $role->syncPermissions($permissionModels);
        } else {
            $role->syncPermissions([]); // Si no se envían permisos, se quitan todos los asignados previamente.
        }

        return redirect()->route('admin.roles.index')
                         ->with('success', 'Rol actualizado exitosamente.');
    }
}
