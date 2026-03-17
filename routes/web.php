<?php

use Illuminate\Support\Facades\Route;
//contoladors
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Auth\ClienteLoginController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\TareaCargadaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObligacionesController;
//nuevas rutas
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\CobroController;
use App\Http\Controllers\ReporteController;
// Nuevos controladores de administración
use App\Http\Controllers\RegimenController;
use App\Http\Controllers\CatalogoServicioController;
use App\Http\Controllers\TipoObligacionController;



Route::get('/', function () {
    return view('welcome');
});


Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('cliente/login', [ClienteLoginController::class, 'showLoginForm'])->name('cliente.login');
Route::post('cliente/login', [ClienteLoginController::class, 'login'])->name('cliente.login');
Route::post('cliente/logout', [ClienteLoginController::class, 'logout'])->name('cliente.logout');


// Ruta del dashboard de clientes (protegida por el guardia 'cliente')
Route::middleware('auth:cliente')->group(function () {
    Route::get('clientes/dashboard', function () {
        return view('clientes.dashboard');
    })->name('clientes.dashboard');
});



Route::middleware('auth:cliente')->group(function () {
    Route::get('/clientes/tareas', [TareaController::class, 'clientestareas'])->name('clientes.tareas');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/tareas/filtros-avanzados', [TareaController::class, 'indexFiltrosAvanzados'])->name('tareas.filtros_avanzados'); 
    Route::get('/tareas/reporte', [ReporteController::class, 'generarReporte'])->name('tareas.reporte');
    Route::get('/tareas/reporte-agrupado', [ReporteController::class, 'generarReporteAgrupado'])->name('tareas.reporte_agrupado'); 
});



Route::group(['middleware'=> ['auth']],function(){

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('roles', RolController::class);
    Route::resource('clientes', ClienteController::class);
    Route::post('/notificar-cliente', [ClienteController::class, 'notificarCliente'])->name('notificar.cliente');
    
    

    Route::resource('tareas', TareaController::class);
    Route::put('tarea/{tarea}/estado', [TareaController::class, 'updateEstado'])->name('tareas.updateEstado');
    Route::get('/tareas/buscar', [TareaController::class, 'buscarPorCedula'])->name('tareas.buscar');
    Route::post('/tareas/{tarea}/notificar', [TareaController::class, 'notificarCliente'])->name('tareas.notificarCliente');
    
    
    // Mostrar formulario para transferir la tarea
    Route::get('/tareas/{id}/transferir', [TareaController::class, 'formTransferir'])->name('tareas.formTransferir');
    // Procesar el formulario de transferencia
    Route::post('/tareas/{id}/transferir', [TareaController::class, 'transferir'])->name('tareas.transferir');
    //excel
    Route::get('/reporte-tareas/excel', [ReporteController::class, 'exportarExcelAgrupado'])->name('reporte.tareas.excel');

    
    Route::get('/importar-excel', function () {
        return view('informacion.importar');
    })->name('importar.excel.form');
    Route::post('/importar-excel', [TareaCargadaController::class, 'importarExcel'])->name('importar.excel');
    
    //rutas para ver 
    Route::get('/tareas-cargadas', [TareaCargadaController::class, 'listarTareasCargadas'])->name('tareas.cargadas');
    Route::put('/tareas-cargadas/{id}', [TareaCargadaController::class, 'editarTareaCargada'])->name('tareas.cargadas.editar');
    Route::delete('/tareas-cargadas/{id}', [TareaCargadaController::class, 'eliminarTareaCargada'])->name('tareas.cargadas.eliminar');
    Route::post('/tareas/procesar', [TareaCargadaController::class, 'procesarTareasDesdeCargadas'])->name('tareas.cargadas.procesar');
    

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/vencimientos', [ObligacionesController::class, 'index'])->name('vencimientos.index');
    Route::post('/vencimientos/notificar', [ObligacionesController::class, 'notificarVencimiento'])->name('vencimientos.notificar');
    Route::patch('/vencimientos/{id}/completar', [ObligacionesController::class, 'marcarCompletado'])->name('vencimientos.marcar-completado');
    Route::post('/vencimientos/generar', [ObligacionesController::class, 'generarManual'])->name('vencimientos.generar');
    Route::post('/clientes/{cliente}/obligacion', [ObligacionesController::class, 'crearParaCliente'])->name('clientes.obligacion.store');
    Route::put('/obligaciones/{obligacion}', [ObligacionesController::class, 'actualizarObligacion'])->name('obligaciones.update');
    Route::delete('/obligaciones/{obligacion}', [ObligacionesController::class, 'eliminarObligacion'])->name('obligaciones.destroy');
    Route::patch('/obligaciones/{id}/anular', [ObligacionesController::class, 'anularObligacion'])->name('obligaciones.anular');
    Route::get('/api/tipos-por-regimen/{regimen}', [ObligacionesController::class, 'tiposPorRegimen'])->name('api.tipos-por-regimen');
    Route::get('/api/catalogo-servicios', [ObligacionesController::class, 'catalogoServicios'])->name('api.catalogo-servicios');

    // Administración: regímenes, catálogo y tipos de obligación
    Route::resource('regimenes', RegimenController::class)->except(['show']);
    Route::resource('tipos-obligacion', TipoObligacionController::class)->only(['index', 'create', 'store', 'update', 'destroy']);
    Route::get('/catalogo-servicios', [CatalogoServicioController::class, 'index'])->name('catalogo.index');
    Route::post('/catalogo-servicios/{catalogoServicio}/toggle-obligacion', [CatalogoServicioController::class, 'toggleObligacion'])->name('catalogo.toggleObligacion');
    Route::post('/catalogo-servicios/{id}/toggle-activo', [CatalogoServicioController::class, 'toggleActivo'])->name('catalogo.toggleActivo');

    Route::resource('facturas', FacturaController::class);
    Route::resource('cobros', CobroController::class);
    Route::get('cobros/create', [CobroController::class, 'create'])->name('cobros.create');
    Route::post('/tareas/store-cobro', [CobroController::class, 'storeCobroDesdeTarea'])->name('tareas.store-cobro');
    Route::post('/importar-cobros', [CobroController::class, 'importarExcel'])->name('importar.cobros');


    //reportes excel con pagos 1 es con fecha de facturarion 2 fecha cumplida
    Route::get('/reporte/excel-con-pagos', [ReporteController::class, 'exportarExcelConPagos'])->name('tareas.excelConPagos');
    Route::get('/reporte/exportar-por-fecha-cumplida', [ReporteController::class, 'exportarExcelPorFechaCumplida'])->name('tareas.exportarPorFechaCumplida');
    Route::get('/reporte/exportarExcelPorFechaCobro', [ReporteController::class, 'exportarExcelPorFechaCobro'])->name('tareas.excelPorFechaCobro');
    Route::get('/reporte/cobros-por-asesor', [ReporteController::class, 'exportarExcelCobrosPorAsesor'])->name('reporte.cobrosPorAsesor');


});




//app y home 
Route::middleware('auth')->group(function () {
    Route::get('/app', [HomeController::class, 'index'])->name('app');
});
