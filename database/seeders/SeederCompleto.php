<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class SeederCompleto extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de permisos
        $permisos = [
            // Roles
            'ver-rol',
            'crear-rol',
            'editar-rol',
            'borrar-rol',

            // Usuarios
            'ver-usuario',
            'crear-usuario',
            'editar-usuario',
            'borrar-usuario',

            // Clientes
            'ver-cliente',
            'crear-cliente',
            'editar-cliente',
            'borrar-cliente',
            'mensajes-cliente',

            // Tareas
            'ver-tarea',
            'crear-tarea',
            'editar-tarea',
            'borrar-tarea',
            'actualizar-estado-tarea',
            'ver-tareas-avanzado',
            'notificar-cliente',
            'transferir-tarea',

            // Otros
            'borrar-tarea-cargada',
            'importar-exel',
            'ver-tarea-cargada',
            'ver-dashboard',
        ];

        // Crear y registrar los permisos
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Crear el rol de administrador
        $rolAdmin = Role::firstOrCreate(['name' => 'Administrador']);

        // Asignar todos los permisos al rol administrador
        $rolAdmin->syncPermissions(Permission::all());

        // Crear el usuario superadministrador
        $superAdmin = User::firstOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'password' => bcrypt('Admin123@'),
            'nombre' => 'Super',
            'apellido' => 'Admin',
            'codigo' => 'C01',
            'estado' => 'Activo',
        ]);

        // Asignar el rol al superadmin
        $superAdmin->assignRole($rolAdmin);
    }
}
