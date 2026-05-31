<?php

namespace App\Http\Controllers;

use App\Domain\Auth\Services\AuthService;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// CU06: Controlador del panel inicial despues de iniciar sesion.
class HomeController extends Controller
{
    // CU06: Inyecta servicio de autenticacion y exige sesion activa.
    public function __construct(
        protected AuthService $authService,
    ) {
        $this->middleware('auth');
    }

    // CU06: Decide si muestra el panel o redirige segun el rol del usuario.
    public function index()
    {
        // CU06: Mide rendimiento del flujo de entrada al panel.
        $inicio = microtime(true);
        // CU01: Usuario autenticado.
        $user = Auth::user();

        // CU06: Consulta si el usuario debe ir a una pantalla especifica.
        $inicioRedirect = microtime(true);
        $redirectRoute = $this->authService->redirectAfterHome($user);

        Log::info('[PERF] HomeController@index redirectAfterHome', [
            'ms' => round((microtime(true) - $inicioRedirect) * 1000, 2),
            'redirect_to' => $redirectRoute,
            'id_user' => $user->id_user ?? null,
        ]);

        // CU06: Si el rol tiene pantalla propia, redirige antes de cargar el panel.
        if ($redirectRoute !== route('home-panel')) {
            Log::info('[PERF] HomeController@index total', [
                'ms' => round((microtime(true) - $inicio) * 1000, 2),
                'resultado' => 'redirect',
                'id_user' => $user->id_user ?? null,
            ]);

            return redirect($redirectRoute);
        }

        // Configuracion institucional que se muestra en el panel.
        $inicioConfiguracion = microtime(true);
        $configuracion = Configuracion::first();

        Log::info('[PERF] HomeController@index configuracion', [
            'ms' => round((microtime(true) - $inicioConfiguracion) * 1000, 2),
            'id_user' => $user->id_user ?? null,
        ]);

        Log::info('[PERF] HomeController@index total', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'resultado' => 'view',
            'id_user' => $user->id_user ?? null,
        ]);

        return view('home', compact('configuracion'));
    }
}
