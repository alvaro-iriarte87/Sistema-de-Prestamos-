<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'prestamo_id',
        'id_cuota', // Agregar el nuevo campo 'id_cuota'
        'fechacuota', // Agregar el nuevo campo 'fechacuota'
        'pagada',
        'no_pagada'
    ];

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }

    public static function crearCuota($datos)
    {
        return self::create($datos);
    }

    public static function cargarCuotasDePrestamo($prestamoId)
    {
        return self::where('prestamo_id', $prestamoId)->get();
    }

    public static function eliminarCuota($id)
    {
        $cuota = self::find($id);
        if ($cuota) {
            $cuota->delete();
            return true;
        }
        return false;
    }
}
