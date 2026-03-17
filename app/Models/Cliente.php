<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\Access\Authorizable as AuthorizableTrait;


class Cliente extends Model implements \Illuminate\Contracts\Auth\Authenticatable
{
    use HasFactory, Authenticatable;

    protected $primaryKey = 'id_clientes'; // Nombre de la clave primaria

    protected $fillable = [
        'nombre_cliente',
        'cedula_cliente',
        'telefono_cliente',
        'regimen_id',
        'digito',
        'estado',
        'actividad',
        'fecha_firma',
        'fecha_facturacion',
        'saldo',
        'email_cliente',
        'password',  // Asegúrate de que el campo password esté en $fillable
        'direccion',
        'claves',
        'id_usuario',
    ];

    // Método para obtener el nombre de usuario (email_cliente)

    public function getAuthIdentifierName()
    {
        return 'id_clientes';
    }
    

    // Método para obtener el valor del identificador (email_cliente)
    public function getAuthIdentifier()
    {
        return $this->id_clientes;
    }

    // Método para obtener el hash de la contraseña
    public function getAuthPassword()
    {
        return $this->password;
    }

    // Método para obtener la "remember token" (si usas 'remember me')
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    // Método para establecer la "remember token"
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }
    // Relación con el modelo User
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

        /**
     * Mutator para extraer automáticamente el noveno dígito de la cédula
     */
    public function setCedulaClienteAttribute($value)
    {
        // Guardar el valor original de la cédula
        $this->attributes['cedula_cliente'] = $value;
        
        // Extraer y guardar el noveno dígito (posición 8 porque empieza en 0)
        if (strlen($value) >= 9) {
            $this->attributes['digito'] = (int) substr($value, 8, 1);
        } else {
            $this->attributes['digito'] = null; // O un valor por defecto si prefieres
        }
    }

    // Relaciones para el rediseño de obligaciones
    public function regimen()
    {
        return $this->belongsTo(Regimen::class, 'regimen_id');
    }

    public function servicios()
    {
        return $this->belongsToMany(CatalogoServicio::class, 'cliente_servicio', 'cliente_id', 'catalogo_servicio_id')
                    ->withPivot('precio_personalizado', 'activo')
                    ->withTimestamps();
    }

    public function obligaciones()
    {
        return $this->hasMany(Obligacion::class, 'cliente_id', 'id_clientes');
    }

    public function factura()
    {
        return $this->hasMany(Factura::class, 'cliente_id', 'id_clientes');
    }
    
}
