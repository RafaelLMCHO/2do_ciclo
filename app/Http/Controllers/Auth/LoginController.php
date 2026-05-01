<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Support\BitacoraLogger;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
        BitacoraLogger::log($request, 'Inicio de sesion', $user);

        return redirect($this->authService->redirectAfterLogin($user));
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            BitacoraLogger::log($request, 'Cierre de sesion', $request->user());
        }

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}