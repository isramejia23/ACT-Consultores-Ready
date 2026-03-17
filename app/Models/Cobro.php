<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cobro extends Model
{
    use HasFactory;

    protected $table = 'cobros';

    protected $fillable = [
        'factura_id',
        'monto',
        'fecha_pago',
        'tipo_pago',
        'numero_recibo',
        'usuario_id',
    ];

    /**
     * Relación: Un cobro pertenece a una factura
     */
    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id', 'id_facturas');
    }

    /**
     * Relación: Un cobro lo registró un usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }
}
