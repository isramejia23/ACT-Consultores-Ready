<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'tareas';

    // Clave primaria
    protected $primaryKey = 'id_tareas';

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'id_clientes',
        'id_usuario',
        'transferida_de',
        'id_factura',
        'numero_factura',
        'fecha_facturada',
        'estado',
        'nombre',
        'fecha_cumplida',
        'archivo',
        'cantidad',
        'precio_unitario',
        'total',
        'observacion',
        'notificado',
        'obligacion_id',
    ];

    // Relación con el modelo Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_clientes', 'id_clientes');
    }


    public function asignadoA()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_factura', 'id_facturas');
    }

    public function transferidaDe()
    {
        return $this->belongsTo(User::class, 'transferida_de');
    }

    public function fueTransferida(): bool
    {
        return $this->transferida_de !== null;
    }

    public function obligacion()
    {
        return $this->belongsTo(Obligacion::class, 'obligacion_id');
    }
}
