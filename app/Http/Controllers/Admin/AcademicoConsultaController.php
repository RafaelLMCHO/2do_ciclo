<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AcademicoConsultaController extends Controller
{
    public function historial(Request $request) // CU25:Método recibe una petición y devuelve una vista
    {
        $datos = $this->datosConsulta($request);
        $datos['actualizarEstudiantesPorCursoGestion'] = true;
        $datos['estudiantesPorCursoGestionRoute'] = 'admin.historial-academico.alumnos';

        return view('admin.academico.historial', $datos); // Retorna la vista con los datos de la consulta academica, incluyendo el historial del alumno seleccionado, siempre y cuando el usuario tenga permiso para acceder a la informacion del alumno.
    }

    public function alumnosHistorial(Request $request) // CU25 y CU26:Método se usa para cargar estudiantes según gestión, curso o búsqueda
    {
        return $this->alumnosPorCursoGestion($request);
    }

    public function alumnosRendimiento(Request $request)
    {
        return $this->alumnosPorCursoGestion($request);
    }

    public function alumnosLibreta(Request $request) // CU26:Metodo se usa para cargar estudiantes segun gestion, curso o busqueda, para mostrar la libreta academica del estudiante seleccionado, siempre y cuando el usuario tenga permiso para acceder a la informacion del estudiante  y el estudiante no tenga pagos pendientes.
    {
        return $this->alumnosPorCursoGestion($request);
    }

    private function alumnosPorCursoGestion(Request $request) // CU25:Este método obtiene estudiantes filtrados por gestión y curso.
    {
        $search = trim((string) $request->input('search'));
        $idGestion = $request->integer('id_gestion') ?: null;
        $idCurso = $request->integer('id_curso') ?: null;

        $alumnos = $this->alumnosDisponibles($request, $search, $idGestion, $idCurso)
            ->limit(120)
            ->get()
            ->map(fn ($alumno) => [
                'id_alumno' => $alumno->id_alumno,
                'ci' => $alumno->ci,
                'nombre_completo' => $alumno->nombre_completo,
            ]);

        return response()->json($alumnos, 200, ['Content-Type' => 'application/json; charset=UTF-8']);
    }

    public function libreta(Request $request) // CU26:Metodo recibe una peticion y devuelve una vista con la libreta academica del estudiante seleccionado, siempre y cuando el usuario tenga permiso para acceder a la informacion del estudiante y el estudiante no tenga pagos pendientes.
    {
        $datos = $this->datosConsulta($request, requiereTrimestre: true);
        $datos['actualizarEstudiantesPorCursoGestion'] = true;
        $datos['estudiantesPorCursoGestionRoute'] = 'admin.libretas.alumnos';
        $datos['pagos'] = $datos['alumnoSeleccionado']
            ? $this->estadoPagos((int) $datos['alumnoSeleccionado']->id_alumno, $datos['idGestion'], $datos['idTrimestre'])
            : $this->estadoPagos(null, null, $datos['idTrimestre']);

        return view('admin.academico.libreta', $datos);
    }

    public function rendimiento(Request $request)
    {
        $datos = $this->datosConsulta($request, incluyeAsistencia: true);
        $datos['actualizarEstudiantesPorCursoGestion'] = true;
        $datos['estudiantesPorCursoGestionRoute'] = 'admin.rendimiento-academico.alumnos';

        if ($datos['alumnoSeleccionado']) {
            $datos['asistencia'] = $this->resumenAsistencia(
                (int) $datos['alumnoSeleccionado']->id_alumno,
                $datos['idGestion'],
                $datos['idCurso'],
                $datos['idMateria']
            );
            $datos['rendimientoMaterias'] = $this->rendimientoPorMateria($datos['notas']);
            $datos['materiasBajoRendimiento'] = $datos['rendimientoMaterias']
                ->filter(fn ($materia) => (float) $materia->promedio < 51);
        } else {
            $datos['asistencia'] = $this->resumenAsistencia(null, null, null, null);
            $datos['rendimientoMaterias'] = collect();
            $datos['materiasBajoRendimiento'] = collect();
        }

        return view('admin.academico.rendimiento', $datos);
    }

    public function imprimirHistorial(Request $request) //CU25:Método genera la vista de impresión/PDF del historial académico.
    {
        return view('admin.academico.print.historial', $this->datosConsulta($request));
    }

    public function imprimirLibreta(Request $request) // CU26:Metodo genera la vista de impresion/PDF de la libreta academica del estudiante seleccionado, siempre y cuando el usuario tenga permiso para acceder a la informacion del estudiante y el estudiante no tenga pagos pendientes.
    {
        abort_if($this->esProfesor($request), 403, 'El docente solo puede visualizar libretas relacionadas con sus asignaciones.');

        $datos = $this->datosConsulta($request, requiereTrimestre: true);
        $datos['pagos'] = $datos['alumnoSeleccionado']
            ? $this->estadoPagos((int) $datos['alumnoSeleccionado']->id_alumno, $datos['idGestion'], $datos['idTrimestre'])
            : $this->estadoPagos(null, null, $datos['idTrimestre']);

        abort_if(! $datos['pagos']['habilitada'], 403, 'La libreta no puede ser impresa por pagos pendientes.');

        return view('admin.academico.print.libreta', $datos);
    }

    public function imprimirRendimiento(Request $request)
    {
        $datos = $this->datosConsulta($request, incluyeAsistencia: true);
        $datos['asistencia'] = $datos['alumnoSeleccionado']
            ? $this->resumenAsistencia((int) $datos['alumnoSeleccionado']->id_alumno, $datos['idGestion'], $datos['idCurso'], $datos['idMateria'])
            : $this->resumenAsistencia(null, null, null, null);
        $datos['rendimientoMaterias'] = $this->rendimientoPorMateria($datos['notas']);
        $datos['materiasBajoRendimiento'] = $datos['rendimientoMaterias']->filter(fn ($materia) => (float) $materia->promedio < 51);

        return view('admin.academico.print.rendimiento', $datos);
    }

    private function datosConsulta(Request $request, bool $requiereTrimestre = false, bool $incluyeAsistencia = false): array // CU25 y CU26:Sirve para preparar los datos generales de la consulta: alumno, gestión, curso, materia, trimestre, notas, etc.
    {
        $search = trim((string) $request->input('search'));
        $idAlumno = $request->integer('id_alumno') ?: null;
        $idGestion = $request->integer('id_gestion') ?: null;
        $idCurso = $request->integer('id_curso') ?: null;
        $idMateria = $request->integer('id_materia') ?: null;
        $idTrimestre = $request->integer('id_trimestre') ?: null;

        $alumnos = $this->alumnosDisponibles($request, $search, $idGestion, $idCurso)->limit(60)->get();

        if (! $idAlumno && $alumnos->count() === 1) {
            $idAlumno = (int) $alumnos->first()->id_alumno;
        }

        $alumnoSeleccionado = $idAlumno ? $this->buscarAlumnoPermitido($request, $idAlumno) : null;
        abort_if($idAlumno && ! $alumnoSeleccionado, 403);

        $notas = collect();
        if ($alumnoSeleccionado) {
            $notas = $this->consultaNotasPermitidas($request)
                ->where('n.id_alumno', $idAlumno)
                ->when($idGestion, fn ($query) => $query->where('n.id_gestion', $idGestion))
                ->when($idCurso, fn ($query) => $query->where('n.id_curso', $idCurso))
                ->when($idMateria, fn ($query) => $query->where('n.id_materia', $idMateria))
                ->when($idTrimestre, fn ($query) => $query->where('n.id_trimestre', $idTrimestre))
                ->get();
        }

        $promedioGeneral = $notas->isNotEmpty() ? round((float) $notas->avg('promediofinal'), 2) : null;

        return [
            'search' => $search,
            'idAlumno' => $idAlumno,
            'idGestion' => $idGestion,
            'idCurso' => $idCurso,
            'idMateria' => $idMateria,
            'idTrimestre' => $idTrimestre,
            'requiereTrimestre' => $requiereTrimestre,
            'incluyeAsistencia' => $incluyeAsistencia,
            'alumnos' => $alumnos,
            'alumnoSeleccionado' => $alumnoSeleccionado,
            'notas' => $notas,
            'notasPorGestion' => $notas->groupBy('gestion'),
            'promedioGeneral' => $promedioGeneral,
            'gestiones' => DB::table('gestion')->orderByDesc('id_gestion')->get(),
            'cursos' => DB::table('curso')->orderBy('nombre')->get(),
            'materias' => DB::table('materia')->orderBy('nombre')->get(),
            'trimestres' => DB::table('trimestre')->orderBy('id_trimestre')->get(),
            'esProfesor' => $this->esProfesor($request),
        ];
    }

    private function consultaNotasPermitidas(Request $request) // CU25 y CU26:Desde aquí se consultan las notas permitidas según el usuario
    {
        return DB::table('nota as n')
            ->join('alumno as a', 'a.id_alumno', '=', 'n.id_alumno')
            ->join('materia as m', 'm.id_materia', '=', 'n.id_materia')
            ->join('gestion as g', 'g.id_gestion', '=', 'n.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'n.id_curso')
            ->join('trimestre as t', 't.id_trimestre', '=', 'n.id_trimestre')
            ->leftJoin('materia_curso_gestion as mcg', function ($join) {
                $join->on('mcg.id_materia', '=', 'n.id_materia')
                    ->on('mcg.id_gestion', '=', 'n.id_gestion')
                    ->on('mcg.id_curso', '=', 'n.id_curso');
            })
            ->when($this->esProfesor($request), function ($query) use ($request) {
                $query->where('mcg.id_profesor', $this->idProfesorAutenticado($request) ?? 0);
            })
            ->when($this->esApoderado($request), function ($query) use ($request) {
                $query->whereExists(function ($subquery) use ($request) {
                    $subquery->select(DB::raw(1))
                        ->from('parentesco as p')
                        ->whereColumn('p.id_alumno', 'n.id_alumno')
                        ->where('p.id_apoderado', $this->idApoderadoAutenticado($request) ?? 0);
                });
            })
            ->select(
                'n.id_alumno',
                'n.id_materia',
                'n.id_gestion',
                'n.id_curso',
                'n.id_trimestre',
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno"),
                'a.ci as ci_alumno',
                'm.nombre as materia',
                'g.nombre as gestion',
                'c.nombre as curso',
                DB::raw("CONCAT('Trimestre ', t.id_trimestre) as trimestre"),
                'n.ser',
                'n.saber',
                'n.hacer',
                'n.autoevaluacion',
                'n.promediofinal',
                'n.descripcion'
            )
            ->orderByDesc('n.id_gestion')
            ->orderBy('c.nombre')
            ->orderBy('m.nombre')
            ->orderBy('n.id_trimestre');
    }

    private function alumnosDisponibles(Request $request, string $search, ?int $idGestion = null, ?int $idCurso = null) // CU25:Método arma la lista de alumnos que pueden mostrarse en el formulario
    {
        $like = '%' . $search . '%';

        return DB::table('alumno as a')
            ->when($search, function ($query) use ($like) {
                $query->where(function ($q) use ($like) {
                    $q->whereRaw("LOWER(CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno)) LIKE LOWER(?)", [$like])
                        ->orWhereRaw('LOWER(a.ci) LIKE LOWER(?)', [$like]);
                });
            })
            ->when($idGestion || $idCurso, function ($query) use ($idGestion, $idCurso) {
                $query->whereExists(function ($subquery) use ($idGestion, $idCurso) {
                    $subquery->select(DB::raw(1))
                        ->from('inscripcion as i')
                        ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
                        ->whereColumn('i.id_alumno', 'a.id_alumno')
                        ->when($idGestion, fn ($q) => $q->where('icg.id_gestion', $idGestion))
                        ->when($idCurso, fn ($q) => $q->where('icg.id_curso', $idCurso));
                });
            })
            ->when($this->esProfesor($request), function ($query) use ($request) {
                $query->whereExists(function ($subquery) use ($request) {
                    $subquery->select(DB::raw(1))
                        ->from('nota as n')
                        ->join('materia_curso_gestion as mcg', function ($join) {
                            $join->on('mcg.id_materia', '=', 'n.id_materia')
                                ->on('mcg.id_gestion', '=', 'n.id_gestion')
                                ->on('mcg.id_curso', '=', 'n.id_curso');
                        })
                        ->whereColumn('n.id_alumno', 'a.id_alumno')
                        ->where('mcg.id_profesor', $this->idProfesorAutenticado($request) ?? 0);
                });
            })
            ->when($this->esApoderado($request), function ($query) use ($request) {
                $query->whereExists(function ($subquery) use ($request) {
                    $subquery->select(DB::raw(1))
                        ->from('parentesco as p')
                        ->whereColumn('p.id_alumno', 'a.id_alumno')
                        ->where('p.id_apoderado', $this->idApoderadoAutenticado($request) ?? 0);
                });
            })
            ->select(
                'a.id_alumno',
                'a.ci',
                'a.nombres',
                'a.ap_paterno',
                'a.ap_materno',
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as nombre_completo")
            )
            ->orderBy('a.ap_paterno')
            ->orderBy('a.ap_materno')
            ->orderBy('a.nombres');
    }

    private function buscarAlumnoPermitido(Request $request, int $idAlumno): ?object // CU25:Método valida que el alumno pueda ser consultado por el usuario según su rol
    {
        return $this->alumnosDisponibles($request, '')
            ->where('a.id_alumno', $idAlumno)
            ->first();
    }

    private function estadoPagos(?int $idAlumno, ?int $idGestion, ?int $idTrimestre = null): array // CU26:Metodo que devuelve el estado de pagos del estudiante seleccionado, segun gestion y trimestre, incluyendo los pagos pendientes y el total pendiente.
    {
        if (! $idAlumno) {
            return ['habilitada' => false, 'pendientes' => collect(), 'totalPendiente' => 0, 'registrados' => false];
        }

        $mesesEsperados = $this->mesesEsperadosPorTrimestre($idTrimestre);
        $estadoMensualidad = $this->columnaEstadoPagoMensual();

        $pagos = DB::table('pago_mensual as pm')
            ->join('gestion as g', 'g.id_gestion', '=', 'pm.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'pm.id_curso')
            ->where('pm.id_alumno', $idAlumno)
            ->when($idGestion, fn ($query) => $query->where('pm.id_gestion', $idGestion))
            ->whereIn('pm.mes', $mesesEsperados)
            ->select(
                'pm.mes',
                'pm.monto',
                'pm.fecha',
                DB::raw($estadoMensualidad . ' as estado'),
                'g.nombre as gestion',
                'c.nombre as curso'
            )
            ->orderByDesc('pm.id_gestion')
            ->orderByRaw("FIELD(pm.mes, 'Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre')")
            ->get();

        $registrados = $pagos->isNotEmpty();

        $pendientes = $pagos
            ->filter(fn ($pago) => trim((string) $pago->estado) === 'Pendiente')
            ->map(fn ($pago) => (object) [
                'mes' => $pago->mes,
                'monto' => $pago->monto,
                'fecha' => $pago->fecha,
                'estado' => 'Pendiente',
                'gestion' => $pago->gestion,
                'curso' => $pago->curso,
            ])
            ->values();

        return [
            'habilitada' => $registrados && $pendientes->isEmpty(),
            'pendientes' => $pendientes,
            'totalPendiente' => round((float) $pendientes->sum('monto'), 2),
            'registrados' => $registrados,
        ];
    }

    private function mesesEsperadosPorTrimestre(?int $idTrimestre): array // CU26:Metodo que devuelve los meses esperados de pago segun el trimestre seleccionado, para validar si el estudiante tiene pagos pendientes.
    {
        return match ($idTrimestre) {
            1 => ['Febrero', 'Marzo', 'Abril'],
            2 => ['Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'],
            3 => ['Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre'],
            default => ['Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre'],
        };
    }

    private function columnaEstadoPagoMensual(): string
    {
        return Schema::hasColumn('pago_mensual', 'estado') ? 'pm.estado' : "'Pagado'";
    }

    private function resumenAsistencia(?int $idAlumno, ?int $idGestion, ?int $idCurso, ?int $idMateria): array
    {
        if (! $idAlumno || ! Schema::hasTable('asistencia')) {
            return ['total' => 0, 'presentes' => 0, 'porcentaje' => null];
        }

        $query = DB::table('asistencia as asi')
            ->join('matricula as mtr', 'mtr.id_matricula', '=', 'asi.id_matricula')
            ->join('inscripcion as i', 'i.id_matricula', '=', 'mtr.id_matricula')
            ->where('i.id_alumno', $idAlumno)
            ->when($idGestion, fn ($query) => $query->where('asi.id_gestion', $idGestion))
            ->when($idCurso, fn ($query) => $query->where('asi.id_curso', $idCurso))
            ->when($idMateria, fn ($query) => $query->where('asi.id_materia', $idMateria));

        $total = (clone $query)->count();
        $presentes = (clone $query)->whereIn('asi.estado', ['P', 'L', 'F'])->count();

        return [
            'total' => $total,
            'presentes' => $presentes,
            'porcentaje' => $total > 0 ? round(($presentes / $total) * 100, 2) : null,
        ];
    }

    private function rendimientoPorMateria(Collection $notas): Collection
    {
        return $notas->groupBy(fn ($nota) => $nota->id_materia . '|' . $nota->id_gestion . '|' . $nota->id_curso)
            ->map(function (Collection $items) {
                $primera = $items->first();

                return (object) [
                    'materia' => $primera->materia,
                    'gestion' => $primera->gestion,
                    'curso' => $primera->curso,
                    'promedio' => round((float) $items->avg('promediofinal'), 2),
                    'mejor' => round((float) $items->max('promediofinal'), 2),
                    'menor' => round((float) $items->min('promediofinal'), 2),
                    'registros' => $items->count(),
                ];
            })
            ->values();
    }

    private function esProfesor(Request $request): bool // CU26:Metodo que valida si el usuario al que se le consulta es un profesor, para limpiar la consulta de notas y estudiantes a los que tiene acceso, segun sus asignaciones.
    {
        return (int) $request->user()?->id_rol === Rol::PROFESOR->value;
    }

    private function esApoderado(Request $request): bool // CU26:Metodo que valida si el usuario al que se le consulta es un apoderado, para limpiar la consulta de notas y estudiantes a los que tiene acceso, segun sus asignaciones.
    {
        return (int) $request->user()?->id_rol === Rol::APODERADO->value;
    }

    private function idProfesorAutenticado(Request $request): ?int // CU26:Metodo que devuelve el ID del profesor autenticado, para limpiar la consulta de notas y estudiantes a los que tiene acceso, segun sus asignaciones.
    {
        return DB::table('profesor')
            ->where('id_user', $request->user()?->id_user)
            ->value('id_profesor');
    }

    private function idApoderadoAutenticado(Request $request): ?int // CU26:Metodo que devuelve el ID del apoderado autenticado, para limpiar la consulta de notas y estudiantes a los que tiene acceso, segun sus asignaciones.
    {
        if (Schema::hasColumn('apoderado', 'id_user')) {
            $idApoderado = DB::table('apoderado')
                ->where('id_user', $request->user()?->id_user)
                ->value('id_apoderado');

            if ($idApoderado) {
                return (int) $idApoderado;
            }
        }

        if (preg_match('/^apoderado_(\d+)$/', (string) $request->user()?->username, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
