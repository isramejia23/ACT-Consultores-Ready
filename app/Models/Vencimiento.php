<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vencimiento extends Model
{
    protected $fillable = [
        'cliente_id',
        'cedula_cliente',
        'nombre_cliente',
        'fecha_vencimiento',
        'regimen',
        'digito',
        'completado',
        'completado_en',
        'notificado'
    ];

    protected $dates = [
        'fecha_vencimiento',
        'generado_en',
        'completado_en'
    ];

    // app/Models/Vencimiento.php
// app/Models/Vencimiento.php
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'vencimiento_id');
    }
}