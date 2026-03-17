<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regimen extends Model
{
    use HasFactory;

    protected $table = 'regimenes';

    protected $fillable = [
        'nombre',
        'periodicidad',
        'mes_vencimiento',
        'dia_fijo'
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'regimen_id', 'id');
    }

    public function tiposObligacion()
    {
        return $this->hasMany(TipoObligacion::class, 'regimen_id');
    }
}
