<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuota;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Prestamo;

class CuotaController extends Controller
{
    // Método para obtener el estado de una cuota específica
    public function obtenerEstadoCuota($id_cuota)
{
    $cuota = Cuota::where('id_cuota', $id_cuota)->first();

    if (!$cuota) {
        return response()->json(['message' => 'Cuota no encontrada'], 404);
    }

    return response()->json([
        'id' => $cuota->id,
        'id_cuota' => $cuota->id_cuota,
        'pagada' => $cuota->pagada
    ]);
}


public function actualizarEstadoCuota($id_cuota, Request $request)
{
    // Validar el estado enviado en la solicitud
    $request->validate([
        'pagada' => 'required|boolean',
    ]);

    // Obtener el estado de la cuota del cuerpo de la solicitud
    $estado = $request->input('pagada');

    // Buscar la cuota por su ID
    $cuota = Cuota::find($id_cuota);

    if (!$cuota) {
        return response()->json(['message' => 'Cuota no encontrada'], 404);
    }

    // Actualizar el estado de la cuota
    $cuota->update(['pagada' => $estado]);

    return response()->json(['message' => 'Estado de la cuota actualizado correctamente']);
}
    // Método para crear una nueva cuota
    public function crearCuota(Request $request)
    {
        // Validar los datos de la solicitud
        $request->validate([
            'id_cuota' => 'required',
            'prestamo_id' => 'required|integer',
            'fechacuota' => 'required|date',
            'pagada' => 'required|boolean',
        ]);
    
        // Crear una nueva instancia de Cuota
        $cuota = new Cuota();
        $cuota->id_cuota = $request->input('id_cuota');
        $cuota->fechacuota = $request->input('fechacuota');
        $cuota->prestamo_id = $request->input('prestamo_id');
        $cuota->pagada = $request->input('pagada');
        // Otros campos y asignaciones de datos de la cuota...
    
        // Guardar la nueva cuota en la base de datos
        $cuota->save();
    
        // Devolver una respuesta con el ID de la nueva cuota
        return response()->json(['message' => 'Cuota creada correctamente', 'cuota_id' => $cuota->id_cuota,'id'=> $cuota->id], 201);
    }
    

    // Método público para crear cuotas automáticamente
    public function crearCuotaAutomaticamente($prestamo_id)
    {
        // Obtener el préstamo
        $prestamo = Prestamo::find($prestamo_id);
    
        // Verificar si se encontró el préstamo
        if (!$prestamo) {
            return response()->json(['message' => 'El préstamo especificado no existe.'], 404);
        }
    
        // Verificar si ya existen cuotas para el préstamo dado
        $cuotasExisten = Cuota::where('prestamo_id', $prestamo_id)->exists();
    
        // Si no existen cuotas, crearlas automáticamente
        if (!$cuotasExisten) {
            // Aquí agregamos la lógica para crear las cuotas automáticamente
    
            // Calcular el intervalo de tiempo entre cuotas según la periodicidad del préstamo
            $intervalo = $prestamo->periodicidad === 'mensual' ? 'months' : 
                         ($prestamo->periodicidad === 'quincenal' ? 'weeks' : 
                         'days');
    
            // Calculamos la cantidad de cuotas del préstamo
            $cuotas = $prestamo->cuotas;
    
            // Obtenemos la fecha de la primera cuota del préstamo
            $fechaCuota = $prestamo->fecha_primera_cuota;
    
            // Crear las cuotas automáticamente
            for ($i = 0; $i < $cuotas; $i++) {
                // Crear una nueva instancia de Cuota
                $cuota = new Cuota();
                $cuota->prestamo_id = $prestamo_id;
                $cuota->fechacuota = $fechaCuota;
                // Otros campos y asignaciones de datos de la cuota...
    
                // Guardar la cuota en la base de datos
                $cuota->save();
    
                // Avanzar la fecha de la siguiente cuota según el intervalo
                $fechaCuota = $fechaCuota->add(1, $intervalo);
            }
    
            return response()->json(['message' => 'Cuotas creadas automáticamente.'], 200);
        } else {
            return response()->json(['message' => 'Ya existen cuotas para el préstamo.'], 200);
        }
    }

    public function index()
    {
        $cuotas = Cuota::all();
        return response()->json($cuotas);
    }
    
}
