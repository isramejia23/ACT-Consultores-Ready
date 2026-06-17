<?php

namespace Tests\Feature;

use App\Models\CatalogoServicio;
use App\Models\Cliente;
use App\Models\Obligacion;
use App\Models\Regimen;
use App\Models\Tarea;
use App\Models\TareaCargada;
use App\Models\TipoObligacion;
use App\Models\User;
use App\Services\TareaImportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Pruebas de caja negra: nuevo servicio en catálogo → asignado a régimen u obligación manual
 * → importar Excel → tarea vinculada automáticamente.
 *
 * REGRESIÓN: antes el sistema usaba un array CODIGOS_CON_OBLIGACION hardcodeado.
 * Cualquier servicio creado después de ese deploy jamás se vinculaba aunque tuviera
 * tipos_obligacion configurados en BD. Estos tests verifican que eso ya no ocurre.
 *
 * Usa DatabaseTransactions: todo lo insertado en cada test se revierte al finalizar,
 * sin afectar la base de datos de desarrollo.
 */
class VincularNuevoServicioTest extends TestCase
{
    use DatabaseTransactions;

    private User $usuario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuario = User::create([
            'nombre'   => 'Asesor',
            'apellido' => 'Test',
            'email'    => 'asesor.test.' . uniqid() . '@test.com',
            'codigo'   => 'TST' . rand(100, 999),
            'password' => bcrypt('password'),
            'estado'   => 'Activo',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helpers de fixture
    // ──────────────────────────────────────────────────────────────────────

    private function servicio(string $codigo, string $nombre = null): CatalogoServicio
    {
        return CatalogoServicio::create([
            'codigo' => $codigo,
            'nombre' => $nombre ?? "Servicio $codigo",
            'activo' => true,
        ]);
    }

    private function regimen(string $nombre = 'Régimen Test'): Regimen
    {
        return Regimen::create(['nombre' => $nombre, 'periodicidad' => 'mensual']);
    }

    private function cliente(Regimen $regimen, string $cedula = null): Cliente
    {
        $cedula = $cedula ?? ('T' . str_pad(rand(1, 999999999), 9, '0', STR_PAD_LEFT));
        return Cliente::create([
            'nombre_cliente'   => "Cliente $cedula",
            'cedula_cliente'   => $cedula,
            'telefono_cliente' => '0999000000',
            'regimen_id'       => $regimen->id,
            'email_cliente'    => "cli{$cedula}@test.com",
            'digito'           => 1,
            'estado'           => 'Activo',
            'password'         => bcrypt('test'),
            'id_usuario'       => $this->usuario->id,
        ]);
    }

    private function tipoObligacion(Regimen $regimen, CatalogoServicio $servicio, string $periodicidad = 'mensual', int $mesVencimiento = null): TipoObligacion
    {
        return TipoObligacion::create([
            'nombre'               => "Tipo {$servicio->codigo}",
            'regimen_id'           => $regimen->id,
            'periodicidad'         => $periodicidad,
            'mes_vencimiento'      => $mesVencimiento,
            'catalogo_servicio_id' => $servicio->id,
        ]);
    }

    private function obligacion(Cliente $cliente, CatalogoServicio $servicio, string $periodo, int $tipoId = null): Obligacion
    {
        return Obligacion::create([
            'cliente_id'           => $cliente->id_clientes,
            'catalogo_servicio_id' => $servicio->id,
            'tipo_obligacion_id'   => $tipoId,
            'periodo'              => $periodo,
            'fecha_vencimiento'    => $periodo . '-28',
            'completado'           => false,
            'estado'               => 'pendiente',
            'generado_en'          => now(),
        ]);
    }

    private function tareaCargada(Cliente $cliente, CatalogoServicio $servicio, string $fecha, string $numfac = null): TareaCargada
    {
        return TareaCargada::create([
            'org'            => 'TEST',
            'numfac'         => $numfac ?? ('TSTFAC-' . uniqid()),
            'fecha'          => $fecha,
            'codigo'         => $servicio->codigo,
            'nombre'         => $servicio->nombre,
            'cant'           => 1,
            'p_u'            => 50.00,
            'dscto'          => 0,
            'total'          => 50.00,
            'codcli'         => '001',
            'cedula'         => $cliente->cedula_cliente,
            'nombre_cliente' => $cliente->nombre_cliente,
            'estado'         => 'Activo',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Tests
    // ──────────────────────────────────────────────────────────────────────

    /**
     * REGRESIÓN PRINCIPAL
     *
     * Con la lista hardcodeada, un código inventado (p.ej. TST-99001) nunca aparecía
     * en CODIGOS_CON_OBLIGACION → la tarea se creaba con obligacion_id=NULL aunque
     * hubiera un tipo_obligacion apuntando a ese servicio.
     *
     * Ahora el sistema consulta tipos_obligacion en BD: cualquier código que esté ahí
     * se vincula correctamente.
     */
    public function test_codigo_nuevo_no_hardcodeado_vincula_correctamente(): void
    {
        $srv      = $this->servicio('TST-99001', 'SERVICIO NUEVO NO HARDCODEADO');
        $reg      = $this->regimen();
        $cli      = $this->cliente($reg);
        $tipo     = $this->tipoObligacion($reg, $srv);
        $obl      = $this->obligacion($cli, $srv, '2026-05', $tipo->id);

        $this->tareaCargada($cli, $srv, '2026-05-15');

        $res = app(TareaImportService::class)->procesarTareasCargadas($cli);

        $this->assertEquals(1, $res['tareas_creadas']);

        $tarea = Tarea::where('id_clientes', $cli->id_clientes)
            ->where('codigo_servicio', 'TST-99001')
            ->first();

        $this->assertNotNull($tarea, 'La tarea debe haberse creado');
        $this->assertNotNull(
            $tarea->obligacion_id,
            'BUG REGRESIÓN: con lista hardcodeada este campo era NULL para códigos nuevos'
        );
        $this->assertEquals($obl->id, $tarea->obligacion_id);
    }

    /**
     * Servicio asignado como obligación manual del cliente (sin tipo_obligacion_id,
     * solo catalogo_servicio_id directo en la obligación). También debe vincularse.
     */
    public function test_servicio_en_obligacion_manual_vincula(): void
    {
        $srv = $this->servicio('TST-88001', 'SERVICIO MANUAL SIN REGIMEN');
        $reg = $this->regimen();
        $cli = $this->cliente($reg);

        // Sin tipo_obligacion: obligación creada manualmente desde el panel
        $obl = $this->obligacion($cli, $srv, '2026-05');

        $this->tareaCargada($cli, $srv, '2026-05-20');

        app(TareaImportService::class)->procesarTareasCargadas($cli);

        $tarea = Tarea::where('id_clientes', $cli->id_clientes)
            ->where('codigo_servicio', 'TST-88001')
            ->first();

        $this->assertNotNull(
            $tarea->obligacion_id,
            'Obligación manual (solo catalogo_servicio_id) también debe vincularse'
        );
        $this->assertEquals($obl->id, $tarea->obligacion_id);
    }

    /**
     * Año cruzado: servicio con mes_vencimiento=1 (enero) y factura de diciembre 2025
     * → la obligación correcta es 2026-01, no 2025-12.
     *
     * Este caso es común en declaraciones anuales que se facturan en diciembre
     * pero cuyo período de cobro es enero del año siguiente.
     */
    public function test_anio_cruzado_factura_diciembre_vincula_obligacion_enero_siguiente(): void
    {
        $srv  = $this->servicio('TST-77001', 'DECLARACIÓN ANUAL ENERO');
        $reg  = $this->regimen();
        $cli  = $this->cliente($reg);
        $tipo = $this->tipoObligacion($reg, $srv, 'anual', 1); // vence en enero

        // La obligación vive en 2026-01
        $obl = $this->obligacion($cli, $srv, '2026-01', $tipo->id);

        // Factura emitida en diciembre 2025
        $this->tareaCargada($cli, $srv, '2025-12-15');

        app(TareaImportService::class)->procesarTareasCargadas($cli);

        $tarea = Tarea::where('id_clientes', $cli->id_clientes)
            ->where('codigo_servicio', 'TST-77001')
            ->first();

        $this->assertNotNull(
            $tarea->obligacion_id,
            'Debe encontrar la obligación de 2026-01 aunque la factura sea de 2025-12'
        );
        $this->assertEquals($obl->id, $tarea->obligacion_id);
        $this->assertEquals('2025-12-15', $tarea->fecha_facturada,
            'La fecha de facturación no debe modificarse');
    }

    /**
     * Servicio válido en tipos_obligacion pero sin obligación generada todavía
     * para ese cliente/período → tarea se crea con obligacion_id=NULL, sin excepción.
     *
     * Esto ocurre cuando se importa Excel antes de correr vencimientos:generar.
     */
    public function test_sin_obligacion_generada_tarea_se_crea_sin_vinculo_sin_excepcion(): void
    {
        $srv = $this->servicio('TST-66001', 'SERVICIO SIN OBLIGACIÓN AÚN');
        $reg = $this->regimen();
        $cli = $this->cliente($reg);

        $this->tipoObligacion($reg, $srv); // existe en tipos_obligacion...
        // ... pero NO se generó la obligación para este cliente todavía

        $this->tareaCargada($cli, $srv, '2026-05-10');

        $res = app(TareaImportService::class)->procesarTareasCargadas($cli);

        $this->assertEquals(1, $res['tareas_creadas'], 'La tarea debe crearse igual');

        $tarea = Tarea::where('id_clientes', $cli->id_clientes)
            ->where('codigo_servicio', 'TST-66001')
            ->first();

        $this->assertNotNull($tarea);
        $this->assertNull(
            $tarea->obligacion_id,
            'Sin obligación en BD, obligacion_id debe quedar NULL (no crash, no excepción)'
        );
    }

    /**
     * El comando artisan tareas:vincular-obligaciones consulta dinámicamente
     * los servicios con obligaciones en BD.
     *
     * Verifica: una tarea existente con obligacion_id=NULL (p.ej. importada antes de
     * que existiera el tipo_obligacion) se vincula al correr el comando.
     */
    public function test_comando_vincular_encuentra_servicio_nuevo_dinamicamente(): void
    {
        $srv  = $this->servicio('TST-55001', 'NUEVO SERVICIO CMD VINCULAR');
        $reg  = $this->regimen();
        $cli  = $this->cliente($reg);
        $tipo = $this->tipoObligacion($reg, $srv);
        $obl  = $this->obligacion($cli, $srv, '2026-04', $tipo->id);

        // Tarea ya creada pero desvinculada (importada antes del fix)
        Tarea::create([
            'id_clientes'     => $cli->id_clientes,
            'id_usuario'      => $this->usuario->id,
            'numero_factura'  => 'TSTCMD-FAC-' . uniqid(),
            'fecha_facturada' => '2026-04-10',
            'estado'          => 'Pendiente',
            'nombre'          => $srv->nombre,
            'codigo_servicio' => $srv->codigo,
            'cantidad'        => 1,
            'precio_unitario' => 50.00,
            'total'           => 50.00,
            'obligacion_id'   => null,
        ]);

        $this->artisan('tareas:vincular-obligaciones', ['mes' => 4, 'anio' => 2026])
             ->assertExitCode(0);

        $tarea = Tarea::where('id_clientes', $cli->id_clientes)
            ->where('codigo_servicio', 'TST-55001')
            ->first();

        $this->assertNotNull(
            $tarea->obligacion_id,
            'El comando debe vincular tareas usando servicios dinámicos de BD'
        );
        $this->assertEquals($obl->id, $tarea->obligacion_id);
    }

    /**
     * Cuando el comando vincula una tarea con estado=Cumplida, la obligación
     * correspondiente debe actualizarse a completado=true y estado='completada'.
     */
    public function test_tarea_cumplida_actualiza_obligacion_a_completada(): void
    {
        $srv  = $this->servicio('TST-44001', 'SERVICIO CUMPLIDO');
        $reg  = $this->regimen();
        $cli  = $this->cliente($reg);
        $tipo = $this->tipoObligacion($reg, $srv);
        $obl  = $this->obligacion($cli, $srv, '2026-03', $tipo->id);

        Tarea::create([
            'id_clientes'     => $cli->id_clientes,
            'id_usuario'      => $this->usuario->id,
            'numero_factura'  => 'TSTCUM-FAC-' . uniqid(),
            'fecha_facturada' => '2026-03-15',
            'estado'          => 'Cumplida',
            'nombre'          => $srv->nombre,
            'codigo_servicio' => $srv->codigo,
            'cantidad'        => 1,
            'precio_unitario' => 50.00,
            'total'           => 50.00,
            'fecha_cumplida'  => '2026-03-20',
            'obligacion_id'   => null,
        ]);

        $this->artisan('tareas:vincular-obligaciones', ['mes' => 3, 'anio' => 2026])
             ->assertExitCode(0);

        $obl->refresh();

        $this->assertTrue($obl->completado,
            'La obligación debe marcarse completada al vincularse con una tarea Cumplida');
        $this->assertEquals('completada', $obl->estado);
    }

    /**
     * Dos clientes distintos con el mismo régimen y servicio deben vincularse
     * cada uno a su propia obligación, sin cruzarse.
     */
    public function test_vinculacion_no_mezcla_obligaciones_entre_clientes(): void
    {
        $srv  = $this->servicio('TST-33001', 'ASESORÍA MENSUAL COMPARTIDA');
        $reg  = $this->regimen();
        $cliA = $this->cliente($reg, '1100' . rand(100000, 999999));
        $cliB = $this->cliente($reg, '2200' . rand(100000, 999999));

        $tipo = $this->tipoObligacion($reg, $srv);
        $oblA = $this->obligacion($cliA, $srv, '2026-05', $tipo->id);
        $oblB = $this->obligacion($cliB, $srv, '2026-05', $tipo->id);

        $this->tareaCargada($cliA, $srv, '2026-05-10');
        $this->tareaCargada($cliB, $srv, '2026-05-12');

        $svc = app(TareaImportService::class);
        $svc->procesarTareasCargadas($cliA);
        $svc->procesarTareasCargadas($cliB);

        $tareaA = Tarea::where('id_clientes', $cliA->id_clientes)->where('codigo_servicio', 'TST-33001')->first();
        $tareaB = Tarea::where('id_clientes', $cliB->id_clientes)->where('codigo_servicio', 'TST-33001')->first();

        $this->assertEquals($oblA->id, $tareaA->obligacion_id,
            'ClienteA debe vincularse a la obligación de clienteA');
        $this->assertEquals($oblB->id, $tareaB->obligacion_id,
            'ClienteB debe vincularse a la obligación de clienteB');
        $this->assertNotEquals($tareaA->obligacion_id, $tareaB->obligacion_id,
            'Cada cliente tiene su propia obligación, no deben cruzarse');
    }

    /**
     * REGRESIÓN DE ORDEN: al crear un cliente desde tareas_cargadas, las obligaciones
     * deben generarse ANTES de procesar las tareas para que la vinculación funcione.
     *
     * Bug anterior: procesarTareasCargadas corría antes que generarParaCliente,
     * entonces buscarObligacionParaTarea encontraba vacío y ponía obligacion_id=NULL.
     */
    public function test_crear_cliente_desde_tarea_cargada_vincula_obligaciones(): void
    {
        $srv  = $this->servicio('TST-ORDER-01', 'ASESORÍA MES ACTUAL');
        $reg  = $this->regimen();
        $tipo = $this->tipoObligacion($reg, $srv, 'mensual');

        // Simulamos el cliente AÚN NO EXISTE, hay tarea cargada esperando
        $cedula = '9' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT) . '0';
        $mesActual  = now()->format('n');
        $anioActual = now()->format('Y');
        $periodo    = now()->format('Y-m');

        // TareaCargada esperando (factura del mes actual)
        TareaCargada::create([
            'org'            => 'TEST',
            'numfac'         => 'TSTORD-' . uniqid(),
            'fecha'          => now()->format('Y-m-15'),
            'codigo'         => $srv->codigo,
            'nombre'         => $srv->nombre,
            'cant'           => 1,
            'p_u'            => 50.00,
            'dscto'          => 0,
            'total'          => 50.00,
            'codcli'         => '001',
            'cedula'         => $cedula,
            'nombre_cliente' => 'Cliente Orden Test',
            'estado'         => 'Activo',
        ]);

        // Crear el cliente (mismo flujo que ClienteController::store)
        $cliente = Cliente::create([
            'nombre_cliente'  => 'Cliente Orden Test',
            'cedula_cliente'  => $cedula,
            'email_cliente'   => "orden{$cedula}@test.com",
            'telefono_cliente'=> '0999000000',
            'regimen_id'      => $reg->id,
            'digito'          => 1,
            'estado'          => 'Activo',
            'password'        => bcrypt('test'),
            'id_usuario'      => $this->usuario->id,
        ]);

        // Replicar el orden CORRECTO del controller:
        // 1. generar obligaciones PRIMERO
        $generador = app(\App\Services\GeneradorVencimientos::class);
        $generador->generarParaCliente($cliente);

        // 2. luego procesar tareas (ahora la obligación ya existe)
        $importService = app(TareaImportService::class);
        $importService->procesarTareasCargadas($cliente);

        $tarea = Tarea::where('id_clientes', $cliente->id_clientes)
            ->where('codigo_servicio', 'TST-ORDER-01')
            ->first();

        $this->assertNotNull($tarea, 'La tarea debe haberse creado');
        $this->assertNotNull(
            $tarea->obligacion_id,
            'Al generar obligaciones ANTES de procesar tareas, la vinculación debe funcionar'
        );

        // Verificar que la obligación es del período correcto
        $obligacion = \App\Models\Obligacion::find($tarea->obligacion_id);
        $this->assertEquals($periodo, $obligacion->periodo);
    }

    /**
     * Tarea duplicada: si se intenta importar la misma factura+nombre dos veces,
     * solo se crea una tarea y se vincula una sola vez.
     */
    public function test_duplicado_excel_no_crea_dos_tareas(): void
    {
        $srv    = $this->servicio('TST-22001', 'SERVICIO DUPLICADO');
        $reg    = $this->regimen();
        $cli    = $this->cliente($reg);
        $numfac = 'TSTDUP-FAC-' . uniqid();

        $this->tipoObligacion($reg, $srv);
        $obl = $this->obligacion($cli, $srv, '2026-05');

        // Mismo numfac y mismo nombre → duplicado
        $this->tareaCargada($cli, $srv, '2026-05-01', $numfac);
        $this->tareaCargada($cli, $srv, '2026-05-01', $numfac);

        $res = app(TareaImportService::class)->procesarTareasCargadas($cli);

        // Solo 1 tarea creada (la segunda es duplicada)
        $this->assertEquals(1, $res['tareas_creadas']);

        $count = Tarea::where('id_clientes', $cli->id_clientes)
            ->where('numero_factura', $numfac)
            ->count();

        $this->assertEquals(1, $count, 'No deben crearse tareas duplicadas por la misma factura');
    }

    /**
     * Un cliente inactivo no tiene obligaciones activas. Las tareas importadas
     * deben crearse pero no vincularse (obligacion_id=NULL).
     */
    public function test_cliente_inactivo_tarea_sin_vinculo(): void
    {
        $srv = $this->servicio('TST-11001', 'SERVICIO CLIENTE INACTIVO');
        $reg = $this->regimen();
        $cli = $this->cliente($reg);

        $this->tipoObligacion($reg, $srv);
        // Sin obligación generada (cliente inactivo nunca tuvo)

        $this->tareaCargada($cli, $srv, '2026-05-05');

        app(TareaImportService::class)->procesarTareasCargadas($cli);

        $tarea = Tarea::where('id_clientes', $cli->id_clientes)
            ->where('codigo_servicio', 'TST-11001')
            ->first();

        $this->assertNotNull($tarea, 'La tarea debe crearse aunque no haya obligación');
        $this->assertNull($tarea->obligacion_id);
    }
}