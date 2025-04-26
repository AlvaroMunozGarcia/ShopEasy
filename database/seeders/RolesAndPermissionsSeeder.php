<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Asegúrate de importar tu modelo User
use Illuminate\Support\Facades\Hash; // Importa Hash para las contraseñas

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Resetear roles y permisos cacheados
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Definir y Crear Permisos (basados en routes/web.php)
        $permissions = [
            'manage categories',
            'manage clients',
            'manage products',
            'manage providers',
            'view purchases',
            'create purchases',
            'cancel purchases',
            'view sales',
            'create sales',
            'cancel sales',
            'view reports',
            'manage settings', // Permiso para Business y Printer (aunque usemos rol Admin)
            'manage users'     // Permiso futuro para administrar usuarios
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // 3. Crear Roles
        $adminRole = Role::create(['name' => 'Admin']);
        $sellerRole = Role::create(['name' => 'Vendedor']);
        // $warehouseRole = Role::create(['name' => 'Almacenista']); // Rol opcional futuro

        // 4. Asignar Permisos a Roles
        // Rol Admin: Todos los permisos
        $adminRole->givePermissionTo(Permission::all());

        // Rol Vendedor: Permisos específicos
        $sellerRole->givePermissionTo([
            'manage clients',
            // 'manage products', // Quizás solo ver o un permiso 'view products' si lo creas
            'view sales',
            'create sales',
            'cancel sales', // O quitar este si no pueden cancelar
            'view purchases', // Quizás puedan ver compras relacionadas a sus ventas o clientes
            // Añade otros permisos si son necesarios para el vendedor
        ]);

        // Rol Almacenista (Ejemplo futuro)
        // $warehouseRole->givePermissionTo([
        //     'manage products',
        //     'manage categories',
        //     'view purchases',
        //     'create purchases', // Si ellos registran entradas
        // ]);

        // 5. (Opcional pero recomendado) Crear Usuarios de Ejemplo y Asignar Roles
        // Usuario Administrador
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@shopeasy.com'], // Clave única para buscar/crear
            [ // Datos a usar si se crea nuevo
                'name' => 'Administrador Principal',
                'password' => Hash::make('password') // ¡Usa una contraseña segura!
            ]
        );
        $adminUser->assignRole($adminRole);

        // Usuario Vendedor
        $sellerUser = User::firstOrCreate(
            ['email' => 'vendedor@shopeasy.com'],
            [
                'name' => 'Usuario Vendedor',
                'password' => Hash::make('password') // ¡Usa una contraseña segura!
            ]
        );
        $sellerUser->assignRole($sellerRole);

        // Usuario de Prueba (el que crea DatabaseSeeder por defecto)
        // Le asignamos un rol o lo dejamos sin rol para probar acceso restringido
        $testUser = User::where('email', 'test@example.com')->first();
        if ($testUser) {
            // $testUser->assignRole($sellerRole); // O asígnale un rol si quieres
             // O no le asignes rol para probar qué pasa si no tiene permisos
        }

    }
}
