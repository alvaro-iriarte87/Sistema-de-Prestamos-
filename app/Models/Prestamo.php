<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $fillable = [
        'monto',
        'fecha',
        'cliente_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function cuotas()
    {
        return $this->hasMany(Cuota::class);
    }

    public static function crearPrestamoParaCliente($datos, $clienteId)
    {
        $datos['cliente_id'] = $clienteId;
        return self::create($datos);
    }

    public static function cargarPrestamosDeCliente($clienteId)
    {
        return self::where('cliente_id', $clienteId)->get();
    }

    public static function eliminarPrestamo($id)
    {
        $prestamo = self::find($id);
        if ($prestamo) {
            $prestamo->delete();
            return true;
        }
        return false;
    }
}

