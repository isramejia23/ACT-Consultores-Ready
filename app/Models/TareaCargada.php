<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaCargada extends Model {
    use HasFactory;

    protected $table = 'tareas_cargadas';

    protected $fillable = [
        'org', 'numfac', 'fecha', 'bo', 'seccion', 'codigo', 'nombre',
        'cant', 'p_u', 'dscto', 'total', 'codcli', 'cedula',
        'nombre_cliente', 'direccion', 'estado'
    ];
}

