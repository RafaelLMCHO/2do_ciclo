<?php

namespace App\Http\Controllers\Apoderado;

use App\Domain\Apoderados\Services\ApoderadoService;
use App\Domain\Auth\Services\AuthService;
use App\Domain\Notas\Services\NotaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// CU04: Controlador relacionado con tutor/apoderado para consultar hijos y notas.
class ConsultaController extends Controller
{
    // CU04: Inyecta servicios de apoderado, notas y autenticacion.
    public function __construct(
        protected ApoderadoService $apoderadoService,
        protected NotaService $notaService,
        protected AuthService $authService,
    ) {
        // CU04: Solo usuarios autenticados pueden consultar informacion de apoderado.
        $this->middleware('auth');
    }

    // CU04: Punto de entrada para consulta de tutor/apoderado o administrador.
    public function index(Request $request)
    {
        // CU04: Marca inicio para medir rendimiento de la consulta.
        $inicio = microtime(true);
        // CU04 y CU01: Obtiene usuario autenticado.
        $user = Auth::user();

        // CU04: Si es administrador, permite consulta general.
        if ($this->authService->esAdmin($user)) {
            Log::info('[PERF] ConsultaController@index ruta', [
                'ms' => round((microtime(true) - $inicio) * 1000, 2),
                'modo' => 'admin',
                'id_user' => $user->id_user ?? null,
            ]);

            return $this->indexComoAdmin($request);
        }

        // CU04: Si es tutor/apoderado, consulta solo sus hijos.
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

        // CU04: Bloquea usuarios sin permiso para esta consulta.
        abort(403);
    }

    // CU04: Consulta notas e hijos del tutor/apoderado autenticado.
    private function indexComoApoderado(Request $request, string $username)
    {
        // CU04: Marca inicio para medir consulta del apoderado.
        $inicio = microtime(true);
        // CU04: Marca inicio para resolver apoderado por username.
        $inicioResolver = microtime(true);
        // CU04: Busca el registro de apoderado vinculado al username.
        $apoderado = $this->apoderadoService->resolverPorUsername($username);

        Log::info('[PERF] ConsultaController@indexComoApoderado resolverPorUsername', [
            'ms' => round((microtime(true) - $inicioResolver) * 1000, 2),
            'username' => $username,
            'encontrado' => (bool) $apoderado,
        ]);

        // CU04: Si no existe apoderado vinculado, muestra vista sin datos.
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

        // CU04: Marca inicio para consultar hijos vinculados.
        $inicioHijos = microtime(true);
        // CU04: Obtiene hijos relacionados por parentesco.
        $hijos = $this->apoderadoService->obtenerHijosDeApoderado((int) $apoderado->id_apoderado);

        Log::info('[PERF] ConsultaController@indexComoApoderado obtenerHijosDeApoderado', [
            'ms' => round((microtime(true) - $inicioHijos) * 1000, 2),
            'id_apoderado' => $apoderado->id_apoderado,
            'cantidad' => $hijos->count(),
        ]);

        // CU04: Lee filtro de hijo seleccionado.
        $hijoSeleccionado = $request->filled('hijo') ? $request->integer('hijo') : null;

        // CU04: Evita que el apoderado consulte alumnos no vinculados.
        if ($hijoSeleccionado && !$hijos->pluck('id_alumno')->contains($hijoSeleccionado)) {
            abort(403);
        }

        // CU04: Marca inicio para consultar notas de hijos.
        $inicioNotas = microtime(true);
        // CU04: Obtiene notas filtradas por apoderado y alumno seleccionado.
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

        // CU04: Muestra vista con hijos y notas agrupadas.
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

    // CU04: Permite al administrador revisar alumnos y apoderados.
    private function indexComoAdmin(Request $request)
    {
        // CU04: Marca inicio para medir consulta administrativa.
        $inicio = microtime(true);
        // CU04: Lee filtro de alumno seleccionado.
        $hijoSeleccionado = $request->filled('hijo') ? $request->integer('hijo') : null;

        // CU04: Marca inicio para cargar alumnos con apoderados.
        $inicioHijos = microtime(true);
        // CU04: Obtiene todos los alumnos con sus apoderados vinculados.
        $hijos = $this->apoderadoService->obtenerTodosLosHijosConApoderados();

        Log::info('[PERF] ConsultaController@indexComoAdmin obtenerTodosLosHijosConApoderados', [
            'ms' => round((microtime(true) - $inicioHijos) * 1000, 2),
            'cantidad' => $hijos->count(),
        ]);

        // CU04: Valida que el alumno filtrado exista.
        if ($hijoSeleccionado && !$hijos->pluck('id_alumno')->contains($hijoSeleccionado)) {
            abort(404);
        }

        // CU04: Marca inicio para consultar notas generales.
        $inicioNotas = microtime(true);
        // CU04: Consulta notas y filtra por alumno cuando corresponde.
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

        // CU04: Muestra vista administrativa con hijos, apoderados y notas.
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
