<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogoServicio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'catalogo_servicios';

    protected $fillable = [
        'codigo',
        'nombre',
        'categoria',
        'genera_obligacion',
        'periodicidad',
        'mes',
        'activo'
    ];

    protected $casts = [
        'genera_obligacion' => 'boolean',
        'activo' => 'boolean',
    ];

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_servicio', 'catalogo_servicio_id', 'cliente_id')
                    ->withPivot('precio_personalizado', 'activo')
                    ->withTimestamps();
    }
}
