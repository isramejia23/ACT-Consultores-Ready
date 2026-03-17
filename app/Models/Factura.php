<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';
    protected $primaryKey = 'id_facturas'; // Muy importante para tu migración actual

    protected $fillable = [
        'numero_factura',
        'fecha_factura',
        'cliente_id',
        'total_factura',
        'saldo_pendiente',
        'estado_pago',
    ];

    /**
     * Relación: Una Factura pertenece a un Cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_clientes');
    }

    /**
     * Relación: Una Factura tiene muchas Tareas
     */
    public function tarea()
    {
        return $this->hasMany(Tarea::class, 'id_factura', 'id_facturas');
    }

    /**
     * Relación: Una Factura tiene muchos Cobros
     */
    public function cobro()
    {
        return $this->hasMany(Cobro::class, 'factura_id', 'id_facturas');
    }
}
