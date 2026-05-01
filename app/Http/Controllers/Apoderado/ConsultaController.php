<?php

namespace App\Http\Controllers\Apoderado;

use App\Domain\Apoderados\Services\ApoderadoService;
use App\Domain\Auth\Services\AuthService;
use App\Domain\Notas\Services\NotaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();

        if ($this->authService->esAdmin($user)) {
            return $this->indexComoAdmin($request);
        }

        if ($this->authService->esApoderado($user)) {
            return $this->indexComoApoderado($request, $user->username);
        }

        abort(403);
    }

    private function indexComoApoderado(Request $request, string $username)
    {
        $apoderado = $this->apoderadoService->resolverPorUsername($username);

        if (!$apoderado) {
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

        $hijos = $this->apoderadoService->obtenerHijosDeApoderado((int) $apoderado->id_apoderado);
        $hijoSeleccionado = $request->filled('hijo') ? $request->integer('hijo') : null;

        if ($hijoSeleccionado && !$hijos->pluck('id_alumno')->contains($hijoSeleccionado)) {
            abort(403);
        }

        $notas = $this->notaService->notasFiltradasPorApoderado(
            (int) $apoderado->id_apoderado,
            $hijoSeleccionado
        );

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
        $hijoSeleccionado = $request->filled('hijo') ? $request->integer('hijo') : null;

        $hijos = $this->apoderadoService->obtenerTodosLosHijosConApoderados();

        if ($hijoSeleccionado && !$hijos->pluck('id_alumno')->contains($hijoSeleccionado)) {
            abort(404);
        }

        $notas = $this->notaService->consultaBaseNotas()
            ->when($hijoSeleccionado, function ($query, $idAlumno) {
                $query->where('n.id_alumno', $idAlumno);
            })
            ->get();

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