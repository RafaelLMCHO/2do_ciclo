<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Auth\Services\AuthService;
use App\Domain\Profesores\Services\ProfesorService;
use App\Http\Controllers\Controller;
use App\Models\Profesor;
use App\Models\ProfesorPermiso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfesorController extends Controller
{
    const MAX_PROFESORES = 20;

    public function __construct(
        protected ProfesorService $profesorService,
        protected AuthService $authService,
    ) {}

    public function index()
    {
        $profesores = Profesor::with(['usuario', 'permiso'])
            ->orderBy('ap_paterno')
            ->orderBy('ap_materno')
            ->orderBy('nombre')
            ->get();

        $totalProfesores = $profesores->count();

        return view('admin.profesores.index', compact('profesores', 'totalProfesores'));
    }

    public function create()
    {
        $total = Profesor::count();

        if ($total >= self::MAX_PROFESORES) {
            return redirect()->route('admin.profesores.index')
                ->with('mensaje', 'Capacidad al tope: no se pueden registrar más de ' . self::MAX_PROFESORES . ' profesores.')
                ->with('icono', 'warning');
        }

        return view('admin.profesores.create');
    }

    public function store(Request $request)
    {
        if (Profesor::count() >= self::MAX_PROFESORES) {
            return redirect()->route('admin.profesores.index')
                ->with('mensaje', 'Capacidad al tope: no se pueden registrar más de ' . self::MAX_PROFESORES . ' profesores.')
                ->with('icono', 'warning');
        }

        $data = $request->validate([
            'nombre'     => ['required', 'string', 'max:50'],
            'ap_paterno' => ['required', 'string', 'max:50'],
            'ap_materno' => ['required', 'string', 'max:50'],
            'ci'         => ['required', 'string', 'max:20'],
            'genero'     => ['required', 'in:M,F'],
            'fecha_nac'  => ['required', 'date'],
            'direccion'  => ['required', 'string', 'max:100'],
            'telefono'   => ['required', 'string', 'max:20'],
            'correo'     => ['required', 'email', 'max:100'],
            'rda'        => ['nullable', 'string', 'max:20'],
            'username'   => ['required', 'string', 'max:50', 'unique:usuario,username'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        DB::transaction(function () use ($data) {
            $usuario = User::create([
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'id_rol'   => 2,
            ]);

            $profesor = Profesor::create([
                'nombre'     => $data['nombre'],
                'ap_paterno' => $data['ap_paterno'],
                'ap_materno' => $data['ap_materno'],
                'ci'         => $data['ci'],
                'genero'     => $data['genero'],
                'fecha_nac'  => $data['fecha_nac'],
                'direccion'  => $data['direccion'],
                'telefono'   => $data['telefono'],
                'correo'     => $data['correo'],
                'rda'        => $data['rda'] ?? '',
                'id_user'    => $usuario->id_user,
            ]);

            $this->profesorService->crearPermisoDefecto($profesor);
        });

        return redirect()->route('admin.profesores.index')
            ->with('mensaje', 'Profesor registrado exitosamente.')
            ->with('icono', 'success');
    }

    public function edit($id)
    {
        $profesor = Profesor::with(['usuario', 'permiso'])->findOrFail($id);

        return view('admin.profesores.edit', compact('profesor'));
    }

    public function update(Request $request, $id)
    {
        $profesor = Profesor::with(['usuario', 'permiso'])->findOrFail($id);

        $data = $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('usuario', 'username')->ignore(optional($profesor->usuario)->id_user, 'id_user'),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'puede_ver_horario' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($profesor, $data, $request) {
            $usuario = $profesor->usuario;

            if (!$usuario) {
                $usuario = User::create([
                    'username' => $data['username'],
                    'password' => Hash::make($data['password'] ?: $this->profesorService->generarPasswordDefault($profesor)),
                    'id_rol' => 2,
                ]);

                $profesor->id_user = $usuario->id_user;
                $profesor->save();
            } else {
                $usuario->username = $data['username'];
                $usuario->id_rol = 2;

                if (!empty($data['password'])) {
                    $usuario->password = Hash::make($data['password']);
                }

                $usuario->save();
            }

            ProfesorPermiso::updateOrCreate(
                ['id_profesor' => $profesor->id_profesor],
                ['puede_ver_horario' => $request->boolean('puede_ver_horario')]
            );
        });

        return redirect()->route('admin.profesores.index')
            ->with('mensaje', 'Profesor actualizado exitosamente.')
            ->with('icono', 'success');
    }

    public function editInfo($id)
    {
        $profesor = Profesor::findOrFail($id);

        return view('admin.profesores.editInfo', compact('profesor'));
    }

    public function updateInfo(Request $request, $id)
    {
        $profesor = Profesor::findOrFail($id);

        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:100'],
            'ap_paterno'  => ['required', 'string', 'max:100'],
            'ap_materno'  => ['nullable', 'string', 'max:100'],
            'ci'          => ['nullable', 'string', 'max:20'],
            'correo'      => ['nullable', 'email', 'max:150'],
            'telefono'    => ['nullable', 'string', 'max:20'],
            'direccion'   => ['nullable', 'string', 'max:255'],
            'genero'      => ['nullable', 'in:M,F'],
            'fecha_nac'   => ['nullable', 'date'],
        ]);

        $profesor->fill($data)->save();

        return redirect()->route('admin.profesores.index')
            ->with('mensaje', 'Datos del profesor actualizados correctamente.')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $profesor = Profesor::with(['usuario', 'permiso'])->findOrFail($id);

        try {
            $this->profesorService->eliminarProfesor($profesor);
        } catch (\Exception $e) {
            return redirect()->route('admin.profesores.index')
                ->with('mensaje', 'No se puede eliminar el profesor: ' . $e->getMessage())
                ->with('icono', 'error');
        }

        return redirect()->route('admin.profesores.index')
            ->with('mensaje', 'Profesor eliminado correctamente.')
            ->with('icono', 'success');
    }
}
