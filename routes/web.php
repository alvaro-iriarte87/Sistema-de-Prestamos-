<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PrestamoController; // Importa el controlador de préstamos
use App\Models\Cliente; // Importa el modelo Cliente
use App\Http\Controllers\CuotaController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/home'); // Redirigir a la página de inicio si el usuario está autenticado
    } else {
        return redirect('/login'); // Redirigir a la página de inicio de sesión si el usuario no está autenticado
    }
});

Auth::routes();

Route::get('/api/user-id', function () {
    return Auth::id();
});

Route::get('/register', 'App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->name('register');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rutas de clientes
Route::put('/api/clientes/{id}', [ClienteController::class, 'update']);
Route::get('/api/clientes', [ClienteController::class, 'index']);
Route::get('/api/clientes/{id}', [ClienteController::class, 'show']);
Route::post('/api/clientes', [ClienteController::class, 'store']);
Route::delete('/api/clientes/{id}', [ClienteController::class, 'destroy']);

// Nueva ruta GET para manejar la solicitud de préstamos vigentes
Route::get('/api/prestamos/vigentes', [PrestamoController::class, 'obtenerPrestamosVigentes']);

Route::get('/api/prestamos/ganancia-usuario/{user_id}', [PrestamoController::class, 'gananciaPorUsuario']);

Route::get('/api/prestamos/monto-total', [PrestamoController::class, 'montoTotal']);
// Ruta existente para manejar la solicitud POST de préstamos
Route::post('/api/prestamos', [PrestamoController::class, 'store']);

Route::get('/api/prestamos/usuario/{user_id}', [PrestamoController::class, 'prestamosPorUsuario']);

Route::get('/api/prestamos/{id}', [PrestamoController::class, 'show']);
// Ruta para eliminar un préstamo
Route::delete('/api/prestamos/{id}', [PrestamoController::class, 'eliminar']);



// Nueva ruta GET para obtener los préstamos de un cliente
Route::get('/api/clientes/{id}/prestamos', function ($id) {
    $cliente = Cliente::with('prestamos')->find($id);
    return response()->json($cliente);
});

Route::get('/api/cuotas/{id}', [CuotaController::class, 'obtenerEstadoCuota']);
Route::get('/api/cuotas', [CuotaController::class, 'index']);
Route::put('/api/cuotas/{id_cuota}', [CuotaController::class, 'actualizarEstadoCuota']);

Route::post('/api/cuotas', [CuotaController::class, 'crearCuota']);
Route::post('/api/prestamos/{prestamo_id}/crear-cuotas', [CuotaController::class, 'crearCuotaAutomaticamente']);
