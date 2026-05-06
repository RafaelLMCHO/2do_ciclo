<?php

namespace App\Http\Controllers\Profesor;

use App\Domain\Auth\Services\AuthService;
use App\Domain\Horarios\Services\HorarioService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $inicio = microtime(true);
        $user = Auth::user();
        $idProfesor = null;

        if ($this->authService->esAdmin($user)) {
            $idProfesor = $request->get('id_profesor', 1);
        } else {
            $inicioProfesorUser = microtime(true);
            $profesor = $this->horarioService->obtenerProfesorPorUserId($user->id_user);

            Log::info('[PERF] HorarioController@index obtenerProfesorPorUserId', [
                'ms' => round((microtime(true) - $inicioProfesorUser) * 1000, 2),
                'id_user' => $user->id_user ?? null,
            ]);

            if ($profesor) {
                $idProfesor = $profesor->id_profesor;
            } else {
                $idProfesor = $this->horarioService->extraerIdProfesorDesdeUsername($user->username);
            }
        }

        $horarios = collect();

        if ($idProfesor) {
            $inicioHorario = microtime(true);
            $horarios = $this->horarioService->obtenerHorarioProfesor((int) $idProfesor);

            Log::info('[PERF] HorarioController@index obtenerHorarioProfesor', [
                'ms' => round((microtime(true) - $inicioHorario) * 1000, 2),
                'id_user' => $user->id_user ?? null,
                'id_profesor' => $idProfesor,
                'cantidad' => $horarios->count(),
            ]);
        }

        $inicioAgrupar = microtime(true);
        $horariosPorDia = $this->horarioService->agruparPorDia($horarios);

        Log::info('[PERF] HorarioController@index agruparPorDia', [
            'ms' => round((microtime(true) - $inicioAgrupar) * 1000, 2),
            'id_user' => $user->id_user ?? null,
        ]);

        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        $inicioProfesorId = microtime(true);
        $profesor = $idProfesor ? $this->horarioService->obtenerProfesorPorId((int) $idProfesor) : null;

        Log::info('[PERF] HorarioController@index obtenerProfesorPorId', [
            'ms' => round((microtime(true) - $inicioProfesorId) * 1000, 2),
            'id_user' => $user->id_user ?? null,
            'id_profesor' => $idProfesor,
        ]);

        $inicioProfesores = microtime(true);
        $profesores = $this->authService->esAdmin($user)
            ? $this->horarioService->obtenerTodosLosProfesores()
            : [];

        Log::info('[PERF] HorarioController@index obtenerTodosLosProfesores', [
            'ms' => round((microtime(true) - $inicioProfesores) * 1000, 2),
            'id_user' => $user->id_user ?? null,
            'es_admin' => $this->authService->esAdmin($user),
            'cantidad' => is_countable($profesores) ? count($profesores) : 0,
        ]);

        Log::info('[PERF] HorarioController@index total', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'id_user' => $user->id_user ?? null,
            'id_profesor' => $idProfesor,
        ]);

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
