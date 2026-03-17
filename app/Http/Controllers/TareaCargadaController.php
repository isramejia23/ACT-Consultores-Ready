<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TareaCargada;
use App\Models\Tarea;
use App\Models\Cliente;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use App\Services\TareaImportService;
use Illuminate\Support\Facades\DB;



class TareaCargadaController extends Controller
{

    public function __construct()
    {
        // Permiso para ver tareas cargadas
        $this->middleware('permission:ver-tarea-cargada', ['only' => ['listarTareasCargadas']]);
        // Permiso para crear tareas cargadas (importar desde Excel)
        $this->middleware('permission:importar-exel', ['only' => ['importarExcel']]);
        // Permiso para editar tareas cargadas
        //$this->middleware('permission:editar-tarea-cargada', ['only' => ['editarTareaCargada']]);
        // Permiso para borrar tareas cargadas
        $this->middleware('permission:borrar-tarea-cargada', ['only' => ['eliminarTareaCargada']]);
    }


    public function importarExcel(Request $request)
    {
        $CodigosDeTareasNoPermitidos = [
            '20001', '20002', '20003', '20004', '20005', '20006', '20007', '20008', '20011'
        ]; //codigos que suman 0
        
        $CodigosSinTotal = [
            '10034', '10067', '10078',
        ];
        
        ini_set('max_execution_time', 300); // 5 minutos
        ini_set('memory_limit', '256M');
        
        // Validar el archivo
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls'
        ]);
        
        $archivoExcel = $request->file('archivo');
        $spreadsheet = IOFactory::load($archivoExcel->getPathname());
        $hoja = $spreadsheet->getActiveSheet();
        $datos = $hoja->toArray(null, true, true, true);
        
        // Inicializar arrays
        $datosProcesados = [];
        $registrosDuplicados = 0;
        
        // Obtener registros existentes en tareas_cargadas para comparación
        $existentes = TareaCargada::select('codigo', 'numfac', 'cedula')
            ->get()
            ->keyBy(function($item) {
                return $item->codigo . '|' . $item->numfac . '|' . $item->cedula;
            });
        
        foreach ($datos as $index => $dato) {
            if ($index === 1) {
                continue; // Saltar la primera fila (encabezado)
            }
            
            // Validar campos obligatorios (incluyendo cédula para evitar NULL)
            if (empty($dato['A']) || empty($dato['B']) || empty($dato['M']) || empty($dato['F'])) {
                continue;
            }
            
            // Verificar si el nombre es prohibidos SE Evita SUBIR COMISIONES BANCARIAS
            if (in_array(strtoupper(trim($dato['F'])), $CodigosDeTareasNoPermitidos)) {
                continue;
            }
            
            // Verificar duplicados en tareas_cargadas
            $claveUnica = $dato['F'] . '|' . $dato['B'] . '|' . $dato['M'];
            if ($existentes->has($claveUnica)) {
                $registrosDuplicados++;
                continue;
            }
            
            // Procesar fecha (manteniendo tu lógica original)
            if (!empty($dato['C'])) {
                $fechaParts = explode('/', $dato['C']);
                if (count($fechaParts) === 3) {
                    $dia = str_pad($fechaParts[0], 2, '0', STR_PAD_LEFT);
                    $mes = str_pad($fechaParts[1], 2, '0', STR_PAD_LEFT);
                    $anio = $fechaParts[2];
                    $fecha = "$anio-$mes-$dia";
                } else {
                    $fecha = null;
                }
            } else {
                $fecha = null;
            }
            
            $total = (float) $dato['K'];
            $codigo = $dato['F'];
            
            // Desactivado: el cambio de precios causa inconsistencias con los reportes de cobros
            // if ($codigo === '20012') {
            //     $total = 10;
            // } elseif (in_array($codigo, $CodigosSinTotal)) {
            //     $total = 0;
            // }
            
            $datosProcesados[] = [
                'org' => $dato['A'],
                'numfac' => $dato['B'],
                'fecha' => $fecha,
                'bo' => $dato['D'],
                'seccion' => $dato['E'],
                'codigo' => $dato['F'],
                'nombre' => $dato['G'],
                'cant' => (float) $dato['H'],
                'p_u' => (float) $dato['I'],
                'dscto' => (float) $dato['J'],
                'total' => $total,
                'codcli' => $dato['L'],
                'cedula' => $dato['M'],
                'nombre_cliente' => $dato['N'],
                'direccion' => $dato['O'],
                'estado' => $dato['P'],
            ];
        }
        
        try {
            DB::beginTransaction();

            // Insertar en lote solo registros no duplicados
            if (!empty($datosProcesados)) {
                TareaCargada::insert($datosProcesados);
            }

            // Procesar tareas cargadas: crear facturas y tareas para clientes existentes
            $importService = app(TareaImportService::class);
            $importService->procesarDesdeDatosImportados($datosProcesados);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
        }

        // Mensaje personalizado
        $mensaje = 'Datos importados y procesados correctamente.';
        if ($registrosDuplicados > 0) {
            $mensaje .= " Se omitieron $registrosDuplicados registros duplicados.";
        }

        return redirect()->back()->with('success', $mensaje);
    }

    // editar, eliminar e index
    public function listarTareasCargadas(Request $request)
    {
        $query = TareaCargada::select([
            'id',
            'numfac',
            'fecha',
            'nombre_cliente',
            'nombre',
            'cedula',
            'direccion'
        ]);

        // Filtro por búsqueda si existe
        if ($request->filled('busqueda')) {
            $busqueda = $request->input('busqueda');
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                ->orWhere('nombre_cliente', 'like', "%{$busqueda}%");
            });
        }

        $tareasCargadas = $query->orderBy('id', 'desc')->paginate(11)->appends($request->all()); // mantiene el filtro al paginar

        $usuarios = \App\Models\User::select(['id', 'nombre','apellido'])->get();

        return view('informacion.tareas_cargadas', compact('tareasCargadas', 'usuarios'));
    }



    public function editarTareaCargada(Request $request, $id)
    {
        $tarea = TareaCargada::findOrFail($id);
        
    
        $tarea->update([
            'org' => $request->org,
            'numfac' => $request->numfac,
            'fecha' => $request->fecha,
            'bo' => $request->bo,
            'seccion' => $request->seccion,
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'cant' => $request->cant,
            'p_u' => $request->p_u,
            'dscto' => $request->dscto,
            'total' => $request->total,
            'codcli' => $request->codcli,
            'cedula' => $request->cedula,
            'nombre_cliente' => $request->nombre_cliente,
            'direccion' => $request->direccion,
            'estado' => $request->estado,
        ]);
    
        return redirect()->route('tareas.cargadas')->with('success', 'Tarea actualizada correctamente.');
    }
    
    public function eliminarTareaCargada($id)
    {
        TareaCargada::findOrFail($id)->delete();
        return redirect()->route('tareas.cargadas')->with('success', 'Tarea eliminada correctamente.');
    }

    // eliminacion de tareas que no deberian estar presentes
    
}


  