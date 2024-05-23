<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'apellido', 'dni', 'direccion', 'telefono', 'celular', 'user_id', 'dniFrente', 'dniDorso'
    ];

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class);
    }

    public static function crearCliente($datos)
    {
        return self::create($datos);
    }

    public static function cargarClientes()
    {
        return self::all();
    }

    public static function eliminarCliente($id)
    {
        $cliente = self::find($id);
        if ($cliente) {
            $cliente->delete();
            return true;
        }
        return false;
    }
    public static function editarCliente($id, $datos)
{
    $cliente = self::find($id);
    if ($cliente) {
        $cliente->update($datos);
        return $cliente;
    }
    return null;
}
}

