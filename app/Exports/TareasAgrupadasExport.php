<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TareasAgrupadasExport implements FromArray, WithHeadings
{
    protected $tareasAgrupadas;

    public function __construct($tareasAgrupadas)
    {
        $this->tareasAgrupadas = $tareasAgrupadas;
    }

    public function array(): array
    {
        $resultado = [];

        foreach ($this->tareasAgrupadas as $asesor => $tareas) {
            foreach ($tareas as $tarea) {
                $resultado[] = [
                    'Asesor' => $asesor,
                    'Cliente' => $tarea->cliente->nombre ?? 'Sin Cliente',
                    'Descripción' => $tarea->descripcion ?? '',
                    'Fecha Facturada' => $tarea->fecha_facturada ?? '',
                    'Estado' => $tarea->estado ?? '',
                ];
            }
        }

        return $resultado;
    }

    public function headings(): array
    {
        return [
            'Asesor',
            'Cliente',
            'Descripción',
            'Fecha Facturada',
            'Estado',
        ];
    }
}
