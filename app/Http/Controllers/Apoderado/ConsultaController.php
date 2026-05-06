<?php

namespace App\Http\Controllers\Apoderado;

use App\Domain\Apoderados\Services\ApoderadoService;
use App\Domain\Auth\Services\AuthService;
use App\Domain\Notas\Services\NotaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConsultaController extends Controller
{
    public function __construct(
        protected ApoderadoService $apoderadoService,
        protected NotaService $notaService,
        protected AuthService $authService,
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $inicio = microtime(true);
        $user = Auth::user();

        if ($this->authService->esAdmin($user)) {
            Log::info('[PERF] ConsultaController@index ruta', [
                'ms' => round((microtime(true) - $inicio) * 1000, 2),
                'modo' => 'admin',
                'id_user' => $user->id_user ?? null,
            ]);

            return $this->indexComoAdmin($request);
        }

        if ($this->authService->esApoderado($user)) {
            Log::info('[PERF] ConsultaController@index ruta', [
                'ms' => round((microtime(true) - $inicio) * 1000, 2),
                'modo' => 'apoderado',
                'id_user' => $user->id_user ?? null,
            ]);

            return $this->indexComoApoderado($request, $user->username);
        }

        Log::info('[PERF] ConsultaController@index ruta', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'modo' => 'sin_permiso',
            'id_user' => $user->id_user ?? null,
        ]);

        abort(403);
    }

    private function indexComoApoderado(Request $request, string $username)
    {
        $inicio = microtime(true);
        $inicioResolver = microtime(true);
        $apoderado = $this->apoderadoService->resolverPorUsername($username);

        Log::info('[PERF] ConsultaController@indexComoApoderado resolverPorUsername', [
            'ms' => round((microtime(true) - $inicioResolver) * 1000, 2),
            'username' => $username,
            'encontrado' => (bool) $apoderado,
        ]);

        if (!$apoderado) {
            Log::info('[PERF] ConsultaController@indexComoApoderado total', [
                'ms' => round((microtime(true) - $inicio) * 1000, 2),
                'resultado' => 'sin_apoderado',
                'username' => $username,
            ]);

            return view('apoderado.consulta', [
                'esAdmin' => false,
                'apoderado' => null,
                'apoderadosFiltro' => collect(),
                'apoderadoSeleccionado' => null,
                'hijos' => collect(),
                'notasPorHijo' => collect(),
                'hijoSeleccionado' => null,
            ]);
        }

        $inicioHijos = microtime(true);
        $hijos = $this->apoderadoService->obtenerHijosDeApoderado((int) $apoderado->id_apoderado);

        Log::info('[PERF] ConsultaController@indexComoApoderado obtenerHijosDeApoderado', [
            'ms' => round((microtime(true) - $inicioHijos) * 1000, 2),
            'id_apoderado' => $apoderado->id_apoderado,
            'cantidad' => $hijos->count(),
        ]);

        $hijoSeleccionado = $request->filled('hijo') ? $request->integer('hijo') : null;

        if ($hijoSeleccionado && !$hijos->pluck('id_alumno')->contains($hijoSeleccionado)) {
            abort(403);
        }

        $inicioNotas = microtime(true);
        $notas = $this->notaService->notasFiltradasPorApoderado(
            (int) $apoderado->id_apoderado,
            $hijoSeleccionado
        );

        Log::info('[PERF] ConsultaController@indexComoApoderado notasFiltradasPorApoderado', [
            'ms' => round((microtime(true) - $inicioNotas) * 1000, 2),
            'id_apoderado' => $apoderado->id_apoderado,
            'hijo_seleccionado' => $hijoSeleccionado,
            'cantidad' => $notas->count(),
        ]);

        Log::info('[PERF] ConsultaController@indexComoApoderado total', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'resultado' => 'view',
            'username' => $username,
        ]);

        return view('apoderado.consulta', [
            'esAdmin' => false,
            'apoderado' => $apoderado,
            'apoderadosFiltro' => collect(),
            'apoderadoSeleccionado' => null,
            'hijos' => $hijos,
            'notasPorHijo' => $notas->groupBy('id_alumno'),
            'hijoSeleccionado' => $hijoSeleccionado,
        ]);
    }

    private function indexComoAdmin(Request $request)
    {
        $inicio = microtime(true);
        $hijoSeleccionado = $request->filled('hijo') ? $request->integer('hijo') : null;

        $inicioHijos = microtime(true);
        $hijos = $this->apoderadoService->obtenerTodosLosHijosConApoderados();

        Log::info('[PERF] ConsultaController@indexComoAdmin obtenerTodosLosHijosConApoderados', [
            'ms' => round((microtime(true) - $inicioHijos) * 1000, 2),
            'cantidad' => $hijos->count(),
        ]);

        if ($hijoSeleccionado && !$hijos->pluck('id_alumno')->contains($hijoSeleccionado)) {
            abort(404);
        }

        $inicioNotas = microtime(true);
        $notas = $this->notaService->consultaBaseNotas()
            ->when($hijoSeleccionado, function ($query, $idAlumno) {
                $query->where('n.id_alumno', $idAlumno);
            })
            ->get();

        Log::info('[PERF] ConsultaController@indexComoAdmin consultaBaseNotas', [
            'ms' => round((microtime(true) - $inicioNotas) * 1000, 2),
            'hijo_seleccionado' => $hijoSeleccionado,
            'cantidad' => $notas->count(),
        ]);

        Log::info('[PERF] ConsultaController@indexComoAdmin total', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'resultado' => 'view',
            'hijo_seleccionado' => $hijoSeleccionado,
        ]);

        return view('apoderado.consulta', [
            'esAdmin' => true,
            'apoderado' => null,
            'apoderadosFiltro' => collect(),
            'apoderadoSeleccionado' => null,
            'hijos' => $hijos,
            'notasPorHijo' => $notas->groupBy('id_alumno'),
            'hijoSeleccionado' => $hijoSeleccionado,
        ]);
    }
}
