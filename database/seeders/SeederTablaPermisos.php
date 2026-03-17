<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SeederTablaPermisos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $permisos=[
        //roles
        // 'ver-rol',
        // 'crear-rol',
        // 'editar-rol',
        // 'borrar-rol',

        // 'ver-usuario',
        // 'crear-usuario',
        // 'editar-usuario',
        // 'borrar-usuario',

        // 'ver-cliente',
        // 'crear-cliente',
        // 'editar-cliente',
        // 'borrar-cliente',
        // 'mensajes-cliente',

        // 'ver-tarea',
        // 'crear-tarea',
        // 'editar-tarea',
        // 'borrar-tarea',
        // 'actualizar-estado-tarea',
        // 'ver-tareas-avanzado',
        // 'notificar-cliente',
        //'transferir-tarea',

        //'ver-factura',
        //'crear-factura',
        //'editar-factura',
        //'borrar-factura',

        //'ver-cobro',
        //'crear-cobro',
        //'editar-cobro',
        //'borrar-cobro',

        // 'borrar-tarea-cargada',
        // 'importar-exel',
        // 'ver-tarea-cargada',
        // 'ver-dashboard',
        

        // Otros existentes
        //'borrar-tarea-cargada',
        //'importar-exel',
        //'ver-tarea-cargada',
        //'ver-dashboard',

        // Regímenes
        //'ver-regimen',
        //'crear-regimen',
        //'editar-regimen',
        //'borrar-regimen',

        // Obligaciones
//        'ver-obligacion',
  //      'crear-obligacion',
    //    'editar-obligacion',
      //  'completar-obligacion',
        //'notificar-obligacion',
        //'generar-obligacion',

        // Tipos de Obligación
//        'ver-tipo-obligacion',
  //      'crear-tipo-obligacion',
    //    'editar-tipo-obligacion',
      //  'borrar-tipo-obligacion',

        // Catálogo de Servicios
//        'ver-catalogo-servicio',
  //      'crear-catalogo-servicio',
    //    'editar-catalogo-servicio',
      //  'borrar-catalogo-servicio',

        // Acceso global por módulo (ve todos los registros, no solo los propios)
        'ver-todos-clientes',
        'ver-todas-tareas',
        'ver-todas-facturas',
        'ver-todos-cobros',
        'ver-todas-obligaciones',

       ];
       foreach($permisos as $permiso){
        Permission::create(['name'=>$permiso]);
       }
    }
}
