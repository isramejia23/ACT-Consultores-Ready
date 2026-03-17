<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClienteServicio extends Pivot
{
    protected $table = 'cliente_servicio';

    protected $fillable = [
        'cliente_id',
        'catalogo_servicio_id',
        'precio_personalizado',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'precio_personalizado' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_clientes');
    }

    public function servicio()
    {
        return $this->belongsTo(CatalogoServicio::class, 'catalogo_servicio_id');
    }
}
