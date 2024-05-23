<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importa la clase Auth
use App\Models\Prestamo;
use Carbon\Carbon;
use App\Models\Cuota; 

class PrestamoController extends Controller
{
    public function store(Request $request)
    {
        // Validación de los datos de la solicitud
        $request->validate([
            // Aquí puedes definir las reglas de validación para los campos del préstamo
        ]);

        // Formatear la fecha y hora en un formato reconocible por MySQL
        $fecha_solicitud = date('Y-m-d H:i:s', strtotime($request->input('fecha_solicitud')));

        // Calcular la fecha de la primera cuota
        $fecha_primera_cuota = $this->calcularFechaPrimeraCuota($fecha_solicitud, $request->input('periodicidad'));

        // Crear un nuevo préstamo en la base de datos
        $prestamo = new Prestamo();
        $prestamo->cliente_id = $request->input('cliente_id'); // Asignar el cliente seleccionado en el formulario
        $prestamo->user_id = Auth::id(); // Asignar el ID del usuario autenticado
        $prestamo->monto = $request->input('monto');
        $prestamo->periodicidad = $request->input('periodicidad');
        $prestamo->interes = $request->input('interes');
        $prestamo->cuotas = $request->input('cuotas');
        $prestamo->fecha_solicitud = $fecha_solicitud; // Usar la fecha y hora formateadas
        $prestamo->fecha_primera_cuota = $fecha_primera_cuota; // Asignar la fecha de la primera cuota calculada
        $prestamo->interestotal = 0; // Asignar un valor predeterminado para interestotal
        $prestamo->total_pagar = $request->input('total_pagar');

        // Guardar el préstamo en la base de datos
        $prestamo->save();

        // Devolver una respuesta
        return response()->json(['message' => 'Préstamo guardado con éxito', 'data' => $prestamo], 201);
    }

    public function eliminar($id)
    {
        // Buscar el préstamo por su ID
        $prestamo = Prestamo::findOrFail($id);

        // Verificar si se encontró el préstamo
        if (!$prestamo) {
            return response()->json(['message' => 'El préstamo no se encontró'], 404);
        }

        // Eliminar el préstamo
        $prestamo->delete();

        // Devolver una respuesta
        return response()->json(['message' => 'Préstamo eliminado con éxito'], 200);
    }

    // Función para calcular la fecha de la primera cuota
    private function calcularFechaPrimeraCuota($fechaSolicitud, $periodicidad)
    {
        $fecha = Carbon::parse($fechaSolicitud); // Convertir la fecha de solicitud a objeto Carbon
        switch ($periodicidad) {
            case 'mensual':
                $fecha->addMonth(); // Agregar un mes a la fecha de solicitud
                break;
            case 'semanal':
                $fecha->addWeek(); // Agregar una semana a la fecha de solicitud
                break;
            case 'quincenal':
                $fecha->addDays(15); // Agregar 15 días a la fecha de solicitud para la quincena
                break;
            // Agrega más casos según tus necesidades
        }
        return $fecha->toDateString(); // Convertir la fecha a una cadena en formato YYYY-MM-DD
    }

    public function obtenerPrestamosVigentes()
{
    // Obtener el ID del usuario autenticado
    $userId = Auth::id();
    
    // Obtener los préstamos vigentes asociados a los clientes del usuario autenticado
    $prestamosVigentes = Prestamo::with('cliente')
        ->where('estado', 'vigente')
        ->whereHas('cliente', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->get();

    // Devolver los préstamos vigentes como una respuesta JSON
    return response()->json($prestamosVigentes);
}

public function actualizarEstadoCuota($prestamo_id, $cuota_id, Request $request)
{
    $estado = $request->input('estado'); // Obtener el estado de la cuota del cuerpo de la solicitud

    $cuota = Cuota::where('prestamo_id', $prestamo_id)->find($cuota_id); // Buscar la cuota por su ID y el ID del préstamo

    if (!$cuota) {
        return response()->json(['message' => 'Cuota no encontrada'], 404);
    }

    $cuota->update(['pagada' => $estado]); // Actualizar el estado de la cuota

    return response()->json(['message' => 'Estado de la cuota actualizado correctamente']);
}
public function show($id)
{
    // Buscar el préstamo por su ID
    $prestamo = Prestamo::with('cliente')->find($id);

    // Verificar si se encontró el préstamo
    if (!$prestamo) {
        return response()->json(['message' => 'El préstamo no se encontró'], 404);
    }

    // Devolver el préstamo como una respuesta JSON
    return response()->json($prestamo);
}
public function prestamosPorUsuario($user_id)
{
    // Buscar los préstamos del usuario por su ID
    $prestamos = Prestamo::where('user_id', $user_id)->get();

    // Verificar si se encontraron préstamos para el usuario
    if ($prestamos->isEmpty()) {
        return response()->json(['message' => 'El usuario no tiene préstamos'], 404);
    }

    // Devolver los préstamos como una respuesta JSON
    return response()->json($prestamos);
}

public function montoTotal()
{
    $userId = auth()->id(); // Obtener el ID del usuario autenticado
    $montoTotal = Prestamo::where('user_id', $userId)->sum('monto');

    return response()->json(['monto_total' => $montoTotal]);
}

public function gananciaPorUsuario($user_id)
    {
        $prestamos = Prestamo::where('user_id', $user_id)->get();
        $gananciaTotal = $prestamos->reduce(function ($carry, $prestamo) {
            return $carry + ($prestamo->total_pagar - $prestamo->monto);
        }, 0);

        return response()->json(['ganancia_total' => $gananciaTotal]);
    }


}