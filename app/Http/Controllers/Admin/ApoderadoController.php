<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Apoderado;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

// CU04: Controlador para gestionar tutores/apoderados y sus estudiantes vinculados.
class ApoderadoController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $apoderados = Apoderado::with(['alumnos', 'usuario'])
            ->when($search, function ($query) use ($search) {
                $query->where('ci', 'like', "%{$search}%")
                    ->orWhere('nombres', 'like', "%{$search}%")
                    ->orWhere('ap_paterno', 'like', "%{$search}%")
                    ->orWhere('ap_materno', 'like', "%{$search}%")
                    ->orWhereHas('usuario', function ($usuarioQuery) use ($search) {
                        $usuarioQuery->where('username', 'like', "%{$search}%");
                    })
                    ->orWhereHas('alumnos', function ($alumnoQuery) use ($search) {
                        $alumnoQuery->where('ci', 'like', "%{$search}%")
                            ->orWhere('nombres', 'like', "%{$search}%")
                            ->orWhere('ap_paterno', 'like', "%{$search}%")
                            ->orWhere('ap_materno', 'like', "%{$search}%");
                    });
            })
            ->orderBy('ap_paterno')
            ->orderBy('ap_materno')
            ->orderBy('nombres')
            ->get();

        return view('admin.apoderados.index', compact('apoderados', 'search'));
    }

    public function create(Request $request)
    {
        $alumnos = $this->alumnosParaSeleccion($request->input('alumno'));

        return view('admin.apoderados.create', compact('alumnos'));
    }

    public function store(Request $request)
    {
        $data = $this->validarApoderado($request);
        $credenciales = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:usuario,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        $alumnos = $this->validarAlumnos($request);
        $descripcionParentesco = $data['descripcion_parentesco'];
        unset($data['descripcion_parentesco']);

        DB::transaction(function () use ($data, $credenciales, $alumnos, $descripcionParentesco) {
            $usuario = User::create([
                'username' => $credenciales['username'],
                'password' => Hash::make($credenciales['password']),
                'id_rol' => Rol::APODERADO->value,
            ]);

            $data['id_user'] = $usuario->id_user;
            $apoderado = Apoderado::create($data);
            $this->sincronizarAlumnos($apoderado, $alumnos, $descripcionParentesco);
        });

        $mensaje = 'Tutor registrado exitosamente.';

        return redirect()->route('admin.apoderados.index')
            ->with('mensaje', $mensaje)
            ->with('icono', 'success');
    }

    public function edit(Apoderado $apoderado)
    {
        $apoderado->load(['alumnos', 'usuario']);
        $alumnos = $this->alumnosParaSeleccion(null);
        $usuario = $apoderado->usuarioConsulta();

        return view('admin.apoderados.edit', compact('apoderado', 'alumnos', 'usuario'));
    }

    public function update(Request $request, Apoderado $apoderado)
    {
        $data = $this->validarApoderado($request, $apoderado);
        $usuarioActual = $apoderado->usuarioConsulta();
        $credenciales = $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('usuario', 'username')->ignore(optional($usuarioActual)->id_user, 'id_user'),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);
        $alumnos = $this->validarAlumnos($request);
        $descripcionParentesco = $data['descripcion_parentesco'];
        unset($data['descripcion_parentesco']);

        DB::transaction(function () use ($apoderado, $data, $credenciales, $alumnos, $descripcionParentesco) {
            $usuario = $apoderado->usuarioConsulta();

            if (!$usuario) {
                $usuario = User::create([
                    'username' => $credenciales['username'],
                    'password' => Hash::make($credenciales['password'] ?: 'apoderado' . $apoderado->id_apoderado),
                    'id_rol' => Rol::APODERADO->value,
                ]);

                $data['id_user'] = $usuario->id_user;
            } else {
                $usuario->username = $credenciales['username'];
                $usuario->id_rol = Rol::APODERADO->value;

                if (!empty($credenciales['password'])) {
                    $usuario->password = Hash::make($credenciales['password']);
                }

                $usuario->save();
                $data['id_user'] = $usuario->id_user;
            }

            $apoderado->update($data);
            $this->sincronizarAlumnos($apoderado, $alumnos, $descripcionParentesco);
        });

        return redirect()->route('admin.apoderados.index')
            ->with('mensaje', 'Tutor actualizado exitosamente.')
            ->with('icono', 'success');
    }

    public function destroy(Apoderado $apoderado)
    {
        if ($this->tieneEstudiantesMatriculados($apoderado)) {
            return redirect()->route('admin.apoderados.index')
                ->with('mensaje', 'No se puede eliminar. El tutor tiene estudiantes matriculados.')
                ->with('icono', 'error');
        }

        try {
            DB::transaction(function () use ($apoderado) {
                $usuario = $apoderado->usuarioConsulta();
                $apoderado->alumnos()->detach();
                $apoderado->delete();
                $usuario?->delete();
            });
        } catch (QueryException $e) {
            return redirect()->route('admin.apoderados.index')
                ->with('mensaje', 'No se puede eliminar el tutor porque tiene registros asociados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.apoderados.index')
            ->with('mensaje', 'Tutor eliminado exitosamente.')
            ->with('icono', 'success');
    }

    private function validarApoderado(Request $request, ?Apoderado $apoderado = null): array
    {
        return $request->validate([
            'ci' => [
                'required',
                'string',
                'max:20',
                Rule::unique('apoderado', 'ci')->ignore($apoderado?->id_apoderado, 'id_apoderado'),
            ],
            'nombres' => ['required', 'string', 'max:50'],
            'ap_paterno' => ['required', 'string', 'max:50'],
            'ap_materno' => ['required', 'string', 'max:50'],
            'genero' => ['required', Rule::in(['M', 'F'])],
            'ocupacion' => ['required', 'string', 'max:50'],
            'fecha_nac' => ['required', 'date'],
            'telefono' => ['required', 'string', 'max:20'],
            'descripcion_parentesco' => ['required', 'string', 'max:30'],
        ], [
            'ci.unique' => 'Ya existe una persona con ese CI.',
        ]);
    }

    private function validarAlumnos(Request $request): array
    {
        $data = $request->validate([
            'alumnos' => ['required', 'array', 'min:1'],
            'alumnos.*' => ['integer', 'exists:alumno,id_alumno'],
        ], [
            'alumnos.required' => 'Debe vincular al menos un estudiante existente.',
            'alumnos.*.exists' => 'Uno de los estudiantes seleccionados no existe.',
        ]);

        return array_unique($data['alumnos']);
    }

    private function sincronizarAlumnos(Apoderado $apoderado, array $alumnos, string $descripcion): void
    {
        $syncData = [];

        foreach ($alumnos as $idAlumno) {
            $syncData[$idAlumno] = ['descripcion' => $descripcion];
        }

        $apoderado->alumnos()->sync($syncData);
    }

    private function alumnosParaSeleccion(?string $search = null)
    {
        $search = trim((string) $search);

        return Alumno::query()
            ->when($search, function ($query) use ($search) {
                $query->where('ci', 'like', "%{$search}%")
                    ->orWhere('nombres', 'like', "%{$search}%")
                    ->orWhere('ap_paterno', 'like', "%{$search}%")
                    ->orWhere('ap_materno', 'like', "%{$search}%");
            })
            ->orderBy('ap_paterno')
            ->orderBy('ap_materno')
            ->orderBy('nombres')
            ->limit(120)
            ->get();
    }

    private function tieneEstudiantesMatriculados(Apoderado $apoderado): bool
    {
        return DB::table('parentesco as p')
            ->join('inscripcion as i', 'i.id_alumno', '=', 'p.id_alumno')
            ->where('p.id_apoderado', $apoderado->id_apoderado)
            ->exists();
    }
}
