<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Http\Request;

// CU05: Controlador para consultar la bitacora del sistema.
class BitacoraController extends Controller
{
    // CU05: Lista las acciones registradas en bitacora.
    public function index()
    {
        // CU05: Carga registros con el usuario que genero cada accion.
        $bitacoras = Bitacora::with('usuario')->orderBy('fecha_hora', 'desc')->get();
        // CU05: Muestra la vista administrativa de bitacora.
        return view('admin.bitacora.index', compact('bitacoras'));
    }
}
