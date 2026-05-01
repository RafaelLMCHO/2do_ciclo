<?php

namespace App\Http\Controllers;

use App\Domain\Auth\Services\AuthService;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct(
        protected AuthService $authService,
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        $redirectRoute = $this->authService->redirectAfterHome($user);

        if ($redirectRoute !== route('home-panel')) {
            return redirect($redirectRoute);
        }

        $configuracion = Configuracion::first();
        return view('home', compact('configuracion'));
    }
}