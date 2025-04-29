<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role; // Importa el modelo Role de Spatie
use Illuminate\Http\Request; // Aunque no se usa directamente en index/show, es bueno tenerlo por si se expande

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

    // --- Métodos Create, Store, Edit, Update, Destroy OMITIDOS intencionalmente ---
    // Como discutimos, generalmente no son necesarios o deseables para roles
    // que se manejan principalmente a través de seeders/migrations.
    // Se pueden añadir en el futuro si la lógica de negocio lo requiere explícitamente.

}
