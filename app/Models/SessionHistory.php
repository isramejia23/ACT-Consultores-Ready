<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionHistory extends Model
{
    protected $table = 'session_history';
    protected $guarded = [];
    protected $dates = [
        'login_at',
        'logout_at',
        'last_activity_at',
        'created_at',
        'updated_at'
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Evento creating para guardar el email automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->user_id) {
                $model->user_email = User::find($model->user_id)->email;
            }
        });
    }
}