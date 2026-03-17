<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Obligacion;
use App\Models\TipoObligacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GeneradorVencimientos
{
    const DIAS_POR_DIGITO = [
        1 => 10, 2 => 12, 3 => 14, 4 => 16, 
        5 => 18, 6 => 20, 7 => 22, 8 => 24, 
        9 => 26, 0 => 28
    ];

    /**
     * Genera las obligaciones del mes/año actual (llamado desde el cron job).
     */
    public function generarVencimientosDelMes(): int
    {
        $now = now();
        return $this->generarParaMes((int) $now->month, (int) $now->year);
    }

    /**
     * Genera obligaciones de régimen para un cliente específico en el mes/año actual.
     */
    public function generarParaCliente(Cliente $cliente): int
    {
        $generadas = 0;
        $mes = (int) now()->month;
        $anio = (int) now()->year;

        if (!$cliente->regimen) {
            return 0;
        }

        $cliente->load('regimen.tiposObligacion');

        foreach ($cliente->regimen->tiposObligacion as $tipoObligacion) {
            $fechaVencimiento = $this->calcularVencimientoParaMes($cliente, $tipoObligacion, $mes, $anio);

            if ($fechaVencimiento && $this->debeGenerarEnMes($tipoObligacion, $mes)) {
                $this->crearRegistroObligacion($cliente, $tipoObligacion, $fechaVencimiento);
                $generadas++;
            }
        }

        return $generadas;
    }

    /**
     * Genera obligaciones para un mes/año específico (llamado desde el controller manual).
     */
    public function generarParaMes(int $mes, int $anio): int
    {
        $generadas = 0;
        $clientes = Cliente::with('regimen.tiposObligacion')->where('estado', 'Activo')->get();

        foreach ($clientes as $cliente) {
            // 1. Obligaciones automáticas (por régimen)
            if ($cliente->regimen) {
                foreach ($cliente->regimen->tiposObligacion as $tipoObligacion) {
                    $fechaVencimiento = $this->calcularVencimientoParaMes($cliente, $tipoObligacion, $mes, $anio);

                    if ($fechaVencimiento && $this->debeGenerarEnMes($tipoObligacion, $mes)) {
                        $this->crearRegistroObligacion($cliente, $tipoObligacion, $fechaVencimiento);
                        $generadas++;
                    }
                }
            }

            // 2. Obligaciones manuales (del catálogo) - regenerar según periodicidad
            $generadas += $this->regenerarObligacionesManuales($cliente, $mes, $anio);
        }

        return $generadas;
    }

    /**
     * Regenera obligaciones manuales (catalogo_servicio_id) según su periodicidad.
     */
    protected function regenerarObligacionesManuales(Cliente $cliente, int $mes, int $anio): int
    {
        $generadas = 0;
        $periodo = sprintf('%04d-%02d', $anio, $mes);

        // Obtener las obligaciones manuales distintas de este cliente
        $manuales = Obligacion::where('cliente_id', $cliente->id_clientes)
            ->whereNotNull('catalogo_servicio_id')
            ->whereNotNull('periodicidad')
            ->select('catalogo_servicio_id', 'periodicidad', 'dia_vencimiento')
            ->distinct()
            ->get();

        foreach ($manuales as $manual) {
            if ($manual->periodicidad === 'anual' && $mes !== 12) {
                continue;
            }

            $existe = Obligacion::where('cliente_id', $cliente->id_clientes)
                ->where('catalogo_servicio_id', $manual->catalogo_servicio_id)
                ->where('periodo', $periodo)
                ->exists();

            if ($existe) {
                continue;
            }

            $dia = $manual->dia_vencimiento ?? 1;
            $diasEnMes = Carbon::create($anio, $mes)->daysInMonth;
            $fecha = Carbon::create($anio, $mes, min($dia, $diasEnMes));

            try {
                Obligacion::create([
                    'cliente_id'           => $cliente->id_clientes,
                    'catalogo_servicio_id' => $manual->catalogo_servicio_id,
                    'periodicidad'         => $manual->periodicidad,
                    'dia_vencimiento'      => $manual->dia_vencimiento,
                    'fecha_vencimiento'    => $fecha,
                    'periodo'              => $periodo,
                    'completado'           => false,
                    'generado_en'          => now(),
                ]);
                $generadas++;
            } catch (\Exception $e) {
                Log::error("Error regenerando obligación manual para cliente {$cliente->id_clientes}: {$e->getMessage()}");
            }
        }

        return $generadas;
    }

    protected function debeGenerarEnMes(TipoObligacion $tipoObligacion, int $mes): bool
    {
        return match(strtolower($tipoObligacion->periodicidad)) {
            'mensual'   => true,
            'semestral' => in_array($mes, [1, 7]),
            'anual'     => $tipoObligacion->mes_vencimiento
                            ? $tipoObligacion->mes_vencimiento == $mes
                            : true, // si no tiene mes propio, delega a calcularVencimientoParaMes
            default     => true,
        };
    }

    protected function debeGenerarEsteMes(TipoObligacion $tipoObligacion, Carbon $fechaVencimiento): bool
    {
        return $this->debeGenerarEnMes($tipoObligacion, now()->month);
    }

    protected function crearRegistroObligacion(Cliente $cliente, TipoObligacion $tipoObligacion, Carbon $fechaVencimiento)
    {
        try {
            $existe = Obligacion::where('cliente_id', $cliente->id_clientes)
                ->where('tipo_obligacion_id', $tipoObligacion->id)
                ->where('periodo', $fechaVencimiento->format('Y-m'))
                ->exists();

            if (!$existe) {
                Obligacion::create([
                    'cliente_id'         => $cliente->id_clientes,
                    'tipo_obligacion_id' => $tipoObligacion->id,
                    'periodo'            => $fechaVencimiento->format('Y-m'),
                    'fecha_vencimiento'  => $fechaVencimiento,
                    'completado'         => false,
                    'generado_en'        => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error generando obligacion para cliente {$cliente->id_clientes}: {$e->getMessage()}");
        }
    }

    protected function calcularVencimiento(Cliente $cliente, TipoObligacion $tipoObligacion): ?Carbon
    {
        $now = now();
        return $this->calcularVencimientoParaMes($cliente, $tipoObligacion, (int) $now->month, (int) $now->year);
    }

    /**
     * Calcula la fecha de vencimiento para un mes/año concreto.
     */
    protected function calcularVencimientoParaMes(Cliente $cliente, TipoObligacion $tipoObligacion, int $mes, int $anio): ?Carbon
    {
        $diaVencimiento = $this->getDiaVencimiento($cliente, $tipoObligacion);

        switch (strtolower($tipoObligacion->periodicidad)) {
            case 'mensual':
                return Carbon::createFromDate($anio, $mes, 1)->endOfMonth()->min(
                    Carbon::createFromDate($anio, $mes, $diaVencimiento)
                );
            case 'semestral':
                if (!in_array($mes, [1, 7])) {
                    return null;
                }
                return Carbon::createFromDate($anio, $mes, $diaVencimiento);
            case 'anual':
                // Prioridad: tipo_obligacion.mes_vencimiento > regimen.mes_vencimiento
                $mesVencimiento = $tipoObligacion->mes_vencimiento
                    ?? $cliente->regimen->mes_vencimiento
                    ?? $mes;
                if ($mesVencimiento != $mes) {
                    return null;
                }
                return Carbon::createFromDate($anio, $mesVencimiento, $diaVencimiento);
            default:
                return null;
        }
    }

    /**
     * Prioridad del día: tipo_obligacion.dia_vencimiento > regimen.dia_fijo > 9no dígito cédula
     */
    protected function getDiaVencimiento(Cliente $cliente, TipoObligacion $tipoObligacion)
    {
        // 1. Día propio del tipo de obligación
        if (!empty($tipoObligacion->dia_vencimiento)) {
            return $tipoObligacion->dia_vencimiento;
        }

        // 2. Día fijo del régimen
        if (!empty($cliente->regimen->dia_fijo)) {
            return $cliente->regimen->dia_fijo;
        }

        // 3. Cálculo por 9no dígito de cédula
        $cedula = $cliente->cedula_cliente;
        $digito = (strlen($cedula) >= 9) ? intval((string)$cedula[8]) : 1;

        return self::DIAS_POR_DIGITO[$digito] ?? 10;
    }

    protected function calcularMensual($diaVencimiento, Carbon $fechaBase)
    {
        $fecha = $fechaBase->copy()->setDay($diaVencimiento);
        
        // If the calculated day is already past in the current month, schedule for next month
        return $fecha->isPast() ? $fecha->addMonth() : $fecha;
    }

    protected function calcularSemestral($diaVencimiento, Carbon $fechaBase)
    {
        $mesesSemestrales = [1, 7];
        
        // Find the next semester month
        $currentMonth = $fechaBase->month;
        $targetMonth = ($currentMonth > 1 && $currentMonth <= 7) ? 7 : 1;
        $targetYear = ($targetMonth == 1 && $currentMonth > 7) ? $fechaBase->year + 1 : $fechaBase->year;

        $fecha = Carbon::create($targetYear, $targetMonth, $diaVencimiento);

        return $fecha->isPast() ? $fecha->addMonths(6) : $fecha;
    }

    protected function calcularAnual($diaVencimiento, Carbon $fechaBase, $mesDeclaracion)
    {
        $fecha = $fechaBase->copy()
            ->month($mesDeclaracion)
            ->setDay($diaVencimiento);

        return $fecha->isPast() ? $fecha->addYear() : $fecha;
    }
}