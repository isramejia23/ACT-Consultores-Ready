<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoObligacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_obligacion';

    protected $fillable = [
        'nombre',
        'regimen_id',
        'periodicidad',
        'mes_vencimiento',
        'dia_vencimiento',
        'catalogo_servicio_id'
    ];

    public function regimen()
    {
        return $this->belongsTo(Regimen::class, 'regimen_id');
    }

    public function servicio()
    {
        return $this->belongsTo(CatalogoServicio::class, 'catalogo_servicio_id');
    }
}
