<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. CREAR PERMISOS (Solo en Español)
        // Usuarios
        Permission::create(['name' => 'ver usuarios']);
        Permission::create(['name' => 'crear usuarios']);
        Permission::create(['name' => 'editar usuarios']);
        Permission::create(['name' => 'eliminar usuarios']);

        // Roles
        Permission::create(['name' => 'ver roles']);
        Permission::create(['name' => 'asignar roles']);

        // Clientes
        Permission::create(['name' => 'ver clientes']);
        Permission::create(['name' => 'crear clientes']);
        Permission::create(['name' => 'editar clientes']);
        Permission::create(['name' => 'eliminar clientes']);

        // Proveedores
        Permission::create(['name' => 'ver proveedores']);
        Permission::create(['name' => 'crear proveedores']);
        Permission::create(['name' => 'editar proveedores']);
        Permission::create(['name' => 'eliminar proveedores']);

        // Productos
        Permission::create(['name' => 'ver productos']);
        Permission::create(['name' => 'crear productos']);
        Permission::create(['name' => 'editar productos']);
        Permission::create(['name' => 'eliminar productos']);

        // Categorías
        Permission::create(['name' => 'ver categorías']);
        Permission::create(['name' => 'crear categorías']);
        Permission::create(['name' => 'editar categorías']);
        Permission::create(['name' => 'eliminar categorías']);

        // Compras
        Permission::create(['name' => 'ver compras']);
        Permission::create(['name' => 'crear compras']);
        Permission::create(['name' => 'ver detalles compra']);
        Permission::create(['name' => 'anular compras']); 

        // Ventas
        Permission::create(['name' => 'ver ventas']);
        Permission::create(['name' => 'crear ventas']);
        Permission::create(['name' => 'ver detalles venta']);
        Permission::create(['name' => 'anular ventas']); 

        // Reportes
        Permission::create(['name' => 'ver reportes']);
        Permission::create(['name' => 'editar configuración']);
        Permission::create(['name' => 'gestionar impresoras']);

        $roleAdmin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $roleVendedor = Role::firstOrCreate(['name' => 'Vendedor', 'guard_name' => 'web']);
        $roleAlmacenista = Role::firstOrCreate(['name' => 'Almacenista', 'guard_name' => 'web']);
        $todosLosPermisos = [
            'ver usuarios', 'crear usuarios', 'editar usuarios', 'eliminar usuarios',
            'ver roles', 'asignar roles',
            'ver clientes', 'crear clientes', 'editar clientes', 'eliminar clientes',
            'ver proveedores', 'crear proveedores', 'editar proveedores', 'eliminar proveedores',
            'ver productos', 'crear productos', 'editar productos', 'eliminar productos',
            'ver categorías', 'crear categorías', 'editar categorías', 'eliminar categorías',
            'ver compras', 'crear compras', 'ver detalles compra', 'anular compras',
            'ver ventas', 'crear ventas', 'ver detalles venta', 'anular ventas',
            'ver reportes',
            'editar configuración', 'gestionar impresoras',
        ];
        $roleAdmin->syncPermissions($todosLosPermisos);

        // Rol Vendedor:
        $roleVendedor->syncPermissions([
            'ver productos',
            'ver clientes', 'crear clientes', 'editar clientes', 'eliminar clientes',
            'ver ventas', 'crear ventas', 'ver detalles venta', 'anular ventas', 
            'ver reportes',
        ]);

        // Rol Almacenista:
        $roleAlmacenista->syncPermissions([
            'ver productos', 'crear productos', 'editar productos', 'eliminar productos',
            'ver categorías', 'crear categorías', 'editar categorías', 'eliminar categorías',
            'ver proveedores', 'crear proveedores', 'editar proveedores', 'eliminar proveedores',
            'ver compras', 'crear compras', 'ver detalles compra', 'anular compras', 
        ]);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@shopeasy.com'],
            [
                'name' => 'Administrador Principal',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole($roleAdmin);

        $vendedorUser = User::firstOrCreate(
            ['email' => 'vendedor@shopeasy.com'],
            [
                'name' => 'Vendedor Principal',
                'password' => Hash::make('password'), 
                'email_verified_at' => now(),
            ]
        );
        $vendedorUser->assignRole($roleVendedor);

        $almacenistaUser = User::firstOrCreate(
            ['email' => 'almacenista@shopeasy.com'],
            [
                'name' => 'Almacenista Principal',
                'password' => Hash::make('password'), 
                'email_verified_at' => now(),
            ]
        );
        $almacenistaUser->assignRole($roleAlmacenista);
        if ($this->command) {
            $this->command->info('Permisos (solo español), Roles y Usuarios base creados/actualizados exitosamente.');
        }
    }
}
