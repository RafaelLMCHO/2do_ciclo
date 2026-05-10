<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Nivel;
use App\Models\Paralelo;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CursoController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $curso_id = $request->input('curso_id');
        $paralelo_id = $request->input('paralelo_id');

        // CU12: Lista y busca cursos por grado, nivel, paralelo o turno.
        $query = Curso::with(['nivel', 'paralelo', 'turno']);

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhere('grado', 'like', "%{$search}%")
                ->orWhereHas('nivel', fn ($q) => $q->where('nombre', 'like', "%{$search}%"))
                ->orWhereHas('paralelo', fn ($q) => $q->where('descripcion', 'like', "%{$search}%"))
                ->orWhereHas('turno', fn ($q) => $q->where('nombre', 'like', "%{$search}%"));
        }

        if ($curso_id) {
            $query->where('id_curso', $curso_id);
        }

        if ($paralelo_id) {
            $query->whereIn('id_curso', function ($q) use ($paralelo_id) {
                $q->select('id_curso')
                  ->from('materia_curso_gestion_paralelo')
                  ->where('id_paralelo', $paralelo_id);
            });
        }

        if ($paralelo_id) {
            $query->with(['paralelos' => function($q) use ($paralelo_id) {
                $q->where('paralelo.id_paralelo', $paralelo_id);
            }]);
        } else {
            $query->with('paralelos');
        }

        $cursos = $query->orderBy('nombre', 'asc')->get();

        $all_cursos = Curso::all();
        $all_paralelos = \App\Models\Paralelo::all();
        
        return view('admin.cursos.index', compact('cursos', 'search', 'all_cursos', 'all_paralelos', 'curso_id', 'paralelo_id'));
    }

    public function create()
    {
        return view('admin.cursos.create', $this->catalogos());
    }

    public function store(Request $request)
    {
        $data = $this->validarCurso($request);
        //$data['nombre'] = $this->nombreCurso($data);

        Curso::create($data);

        return redirect()->route('admin.cursos.index')
            ->with('mensaje', 'Curso creado con éxito')
            ->with('icono', 'success');
    }

    public function edit($id)
    {
        $curso = Curso::findOrFail($id);
        //dd($curso);
        return view('admin.cursos.edit', ['curso' => $curso] + $this->catalogos());
    }

    public function update(Request $request, $id)
    {
        $curso = Curso::findOrFail($id);
        $data = $this->validarCurso($request, $curso->id_curso);
        //$data['nombre'] = $this->nombreCurso($data);
        $curso->update($data);

        return redirect()->route('admin.cursos.index')
            ->with('mensaje', 'Curso actualizado con éxito')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $curso = Curso::findOrFail($id);
        try {
            $curso->delete();
            return redirect()->route('admin.cursos.index')
                ->with('mensaje', 'Curso eliminado con éxito')
                ->with('icono', 'success');
        } catch (\Exception $e) {
            return redirect()->route('admin.cursos.index')
                ->with('mensaje', 'No se puede eliminar el curso porque tiene registros relacionados.')
                ->with('icono', 'error');
        }
    }

    private function validarCurso(Request $request, ?int $id = null): array
    {
        // CU12: No permite duplicar grado, nivel, paralelo y turno.
        return $request->validate([
            'nombre' => [ //MODIFIED era grado
                'required',
                'string',
                'max:50',
                /* Rule::unique('curso', 'grado')
                    ->where('id_nivel', $request->input('id_nivel'))
                    ->where('id_paralelo', $request->input('id_paralelo'))
                    ->where('id_turno', $request->input('id_turno'))
                    ->ignore($id, 'id_curso'), */
            ],
            /* 'id_paralelo' => 'required|exists:paralelo,id_paralelo',
            'id_nivel' => 'required|exists:nivels,id',
            'id_turno' => 'required|exists:turnos,id', */
        ], [
            //'grado.unique' => 'El curso ya existe.',
            'nombre.unique' => 'El curso ya existe.',
        ]);
    }

    private function catalogos(): array
    {
        return [
            'niveles' => Nivel::orderBy('nombre')->get(),
            'paralelos' => Paralelo::orderBy('descripcion')->get(),
            'turnos' => Turno::orderBy('nombre')->get(),
        ];
    }

    private function nombreCurso(array $data): string
    {
        $nivel = Nivel::find($data['id_nivel'])?->nombre;
        $paralelo = Paralelo::find($data['id_paralelo'])?->descripcion;
        $turno = Turno::find($data['id_turno'])?->nombre;

        return trim($data['grado'] . ' ' . $nivel . ' ' . $paralelo . ' ' . $turno);
    }
}
