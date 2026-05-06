<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Support\BitacoraLogger;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    protected $maxAttempts = 2;
    protected $decayMinutes = 1;

    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct(
        protected AuthService $authService,
    ) {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function username()
    {
        return 'username';
    }

    protected function authenticated(Request $request, $user)
    {
        $inicio = microtime(true);

        BitacoraLogger::log($request, 'Inicio de sesion', $user);

        Log::info('[PERF] LoginController@authenticated bitacora', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'id_user' => $user->id_user ?? null,
        ]);

        $inicioRedirect = microtime(true);
        $redirectTo = $this->authService->redirectAfterLogin($user);

        Log::info('[PERF] LoginController@authenticated redirectAfterLogin', [
            'ms' => round((microtime(true) - $inicioRedirect) * 1000, 2),
            'redirect_to' => $redirectTo,
            'id_user' => $user->id_user ?? null,
        ]);

        Log::info('[PERF] LoginController@authenticated total', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'id_user' => $user->id_user ?? null,
        ]);

        return redirect($redirectTo);
    }

    public function logout(Request $request)
    {
        $inicio = microtime(true);
        $user = $request->user();

        if ($user) {
            $inicioBitacora = microtime(true);
            BitacoraLogger::log($request, 'Cierre de sesion', $user);

            Log::info('[PERF] LoginController@logout bitacora', [
                'ms' => round((microtime(true) - $inicioBitacora) * 1000, 2),
                'id_user' => $user->id_user ?? null,
            ]);
        }

        $inicioLogout = microtime(true);
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('[PERF] LoginController@logout sesion', [
            'ms' => round((microtime(true) - $inicioLogout) * 1000, 2),
            'id_user' => $user->id_user ?? null,
        ]);

        Log::info('[PERF] LoginController@logout total', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'id_user' => $user->id_user ?? null,
        ]);

        return redirect('/login');
    }
}
