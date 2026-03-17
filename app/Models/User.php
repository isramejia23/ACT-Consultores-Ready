<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'users'; // Nombre de la tabla

    protected $primaryKey = 'id';


    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'codigo',
        'password',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_usuario');
    }
    public function getRoleNames()
    {
        return $this->roles->pluck('name');
    }
    public function sessionHistory()
    {
        return $this->hasMany(SessionHistory::class);
    }
}
