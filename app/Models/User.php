<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Agregar el campo 'role' para almacenar el rol del usuario
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user is an administrator.
     *
     * @return bool
     */
    public function esAdmin()
    {
        return $this->role === 'admin';
    }

    public function clients()
    {
        return $this->hasMany(Cliente::class);
    }
    public function prestamos()
{
    return $this->hasMany(Prestamo::class);
}
}
