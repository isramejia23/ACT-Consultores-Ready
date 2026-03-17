<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obligacion extends Model
{
    use HasFactory;

    protected $table = 'obligaciones';

    protected $fillable = [
        'cliente_id',
        'tipo_obligacion_id',
        'catalogo_servicio_id',
        'periodicidad',
        'dia_vencimiento',
        'cliente_servicio_id',
        'fecha_vencimiento',
        'periodo',
        'completado',
        'completado_en',
        'estado',
        'generado_en'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'completado' => 'boolean',
        'completado_en' => 'datetime',
        'generado_en' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_clientes');
    }

    public function tipoObligacion()
    {
        return $this->belongsTo(TipoObligacion::class, 'tipo_obligacion_id');
    }

    public function catalogoServicio()
    {
        return $this->belongsTo(CatalogoServicio::class, 'catalogo_servicio_id');
    }

    public function clienteServicio()
    {
        return $this->belongsTo(ClienteServicio::class, 'cliente_servicio_id');
    }

    public function getNombreObligacionAttribute()
    {
        if ($this->tipoObligacion) {
            return $this->tipoObligacion->nombre;
        }
        if ($this->catalogoServicio) {
            return $this->catalogoServicio->nombre;
        }
        return 'N/A';
    }
}
