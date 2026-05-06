<?php

namespace App\Http\Controllers;

use App\Domain\Auth\Services\AuthService;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function __construct(
        protected AuthService $authService,
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        $inicio = microtime(true);
        $user = Auth::user();

        $inicioRedirect = microtime(true);
        $redirectRoute = $this->authService->redirectAfterHome($user);

        Log::info('[PERF] HomeController@index redirectAfterHome', [
            'ms' => round((microtime(true) - $inicioRedirect) * 1000, 2),
            'redirect_to' => $redirectRoute,
            'id_user' => $user->id_user ?? null,
        ]);

        if ($redirectRoute !== route('home-panel')) {
            Log::info('[PERF] HomeController@index total', [
                'ms' => round((microtime(true) - $inicio) * 1000, 2),
                'resultado' => 'redirect',
                'id_user' => $user->id_user ?? null,
            ]);

            return redirect($redirectRoute);
        }

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
