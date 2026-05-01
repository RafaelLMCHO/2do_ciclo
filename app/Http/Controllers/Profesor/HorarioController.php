<?php

namespace App\Http\Controllers\Profesor;

use App\Domain\Auth\Services\AuthService;
use App\Domain\Horarios\Services\HorarioService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HorarioController extends Controller
{
    public function __construct(
        protected HorarioService $horarioService,
        protected AuthService $authService,
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $idProfesor = null;

        if ($this->authService->esAdmin($user)) {
            $idProfesor = $request->get('id_profesor', 1);
        } else {
            $profesor = $this->horarioService->obtenerProfesorPorUserId($user->id_user);

            if ($profesor) {
                $idProfesor = $profesor->id_profesor;
            } else {
                $idProfesor = $this->horarioService->extraerIdProfesorDesdeUsername($user->username);
            }
        }

        $horarios = collect();

        if ($idProfesor) {
            $horarios = $this->horarioService->obtenerHorarioProfesor((int) $idProfesor);
        }

        $horariosPorDia = $this->horarioService->agruparPorDia($horarios);
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        $profesor = $idProfesor ? $this->horarioService->obtenerProfesorPorId((int) $idProfesor) : null;

        $profesores = $this->authService->esAdmin($user)
            ? $this->horarioService->obtenerTodosLosProfesores()
            : [];

        return view('profesor.horario', compact(
            'horariosPorDia',
            'dias',
            'profesor',
            'profesores',
            'user',
            'idProfesor'
        ));
    }
}