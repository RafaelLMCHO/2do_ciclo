<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Alumnos\Services\AlumnoService;
use App\Http\Controllers\Controller;
use App\Models\Alumno;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlumnoController extends Controller
{
    public function __construct(
        protected AlumnoService $alumnoService
    ) {}

    public function index()
    {
        $alumnos = Alumno::with('usuario')
            ->orderBy('ap_paterno')
            ->orderBy('ap_materno')
            ->orderBy('nombres')
            ->get();

        return view('admin.alumnos.index', compact('alumnos'));
    }

    public function create()
    {
        return view('admin.alumnos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ci' => ['required', 'string', 'max:20', 'unique:alumno,ci'],
            'nombres' => ['required', 'string', 'max:50'],
            'ap_paterno' => ['required', 'string', 'max:50'],
            'ap_materno' => ['required', 'string', 'max:50'],
            'genero' => ['required', Rule::in(['F', 'M'])],
            'fecha_nac' => ['required', 'date'],
            'username' => ['required', 'string', 'max:50', 'unique:usuario,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $this->alumnoService->crearConUsuario($data);

        return redirect()->route('admin.alumnos.index')
            ->with('mensaje', 'Alumno creado exitosamente.')
            ->with('icono', 'success');
    }

    public function edit($id)
    {
        $alumno = Alumno::with('usuario')->findOrFail($id);

        return view('admin.alumnos.edit', compact('alumno'));
    }

    public function update(Request $request, $id)
    {
        $alumno = Alumno::with('usuario')->findOrFail($id);

        $data = $request->validate([
            'ci' => ['required', 'string', 'max:20', Rule::unique('alumno', 'ci')->ignore($alumno->id_alumno, 'id_alumno')],
            'nombres' => ['required', 'string', 'max:50'],
            'ap_paterno' => ['required', 'string', 'max:50'],
            'ap_materno' => ['required', 'string', 'max:50'],
            'genero' => ['required', Rule::in(['F', 'M'])],
            'fecha_nac' => ['required', 'date'],
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('usuario', 'username')->ignore(optional($alumno->usuario)->id_user, 'id_user'),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $this->alumnoService->actualizarConUsuario($alumno, $data);

        return redirect()->route('admin.alumnos.index')
            ->with('mensaje', 'Alumno actualizado exitosamente.')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $alumno = Alumno::with('usuario')->findOrFail($id);

        try {
            $this->alumnoService->eliminar($alumno);
        } catch (QueryException $e) {
            return redirect()->route('admin.alumnos.index')
                ->with('mensaje', 'No se puede eliminar el alumno porque tiene registros relacionados en el sistema.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.alumnos.index')
            ->with('mensaje', 'Alumno eliminado exitosamente.')
            ->with('icono', 'success');
    }
}
