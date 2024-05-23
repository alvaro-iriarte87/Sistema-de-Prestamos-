<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Importa la clase Storage
use App\Models\Cliente;

class ClienteController extends Controller
{
    // Método para mostrar todos los clientes
    public function index()
    {
        $clientes = Cliente::where('user_id', Auth::id())->get();
        return response()->json($clientes, 200);
    }

    // Método para almacenar un nuevo cliente
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'dni' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'celular' => 'required'
        ]);

        $cliente = new Cliente();
        $cliente->nombre = $request->nombre;
        $cliente->apellido = $request->apellido;
        $cliente->dni = $request->dni;
        $cliente->direccion = $request->direccion;
        $cliente->telefono = $request->telefono;
        $cliente->celular = $request->celular;
        $cliente->user_id = Auth::id(); // Asigna el ID del usuario autenticado

        if ($request->hasFile('dniFrente')) {
            $path = $request->file('dniFrente')->store('dniFrente');
            $cliente->dniFrente = $path;
        }

        if ($request->hasFile('dniDorso')) {
            $path = $request->file('dniDorso')->store('dniDorso');
            $cliente->dniDorso = $path;
        }

        $cliente->save();

        return response()->json($cliente, 201);
    }

    // Método para eliminar un cliente
    public function destroy($id)
    {
        $cliente = Cliente::find($id);
    
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
    
        if ($cliente->dniFrente) {
            Storage::delete($cliente->dniFrente);
        }
        if ($cliente->dniDorso) {
            Storage::delete($cliente->dniDorso);
        }

        $cliente->delete();
    
        return response()->json(['message' => 'Cliente eliminado correctamente'], 200);
    }

    public function show($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            return response()->json($cliente);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'nombre' => 'nullable',
        'apellido' => 'nullable',
        'dni' => 'nullable',
        'direccion' => 'nullable',
        'telefono' => 'nullable',
        'celular' => 'nullable',
        'dniFrente' => 'nullable|file',
        'dniDorso' => 'nullable|file'
    ]);

    try {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $cliente->nombre = $request->nombre;
        $cliente->apellido = $request->apellido;
        $cliente->dni = $request->dni;
        $cliente->direccion = $request->direccion;
        $cliente->telefono = $request->telefono;
        $cliente->celular = $request->celular;

        if ($request->hasFile('dniFrente')) {
            $path = $request->file('dniFrente')->store('dniFrente');
            $cliente->dniFrente = $path;
        }

        if ($request->hasFile('dniDorso')) {
            $path = $request->file('dniDorso')->store('dniDorso');
            $cliente->dniDorso = $path;
        }

        // Asigna el user_id del usuario autenticado
        $cliente->user_id = Auth::id();

        $cliente->save();

        return response()->json($cliente, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error interno del servidor'], 500);
    }
}
    
}
