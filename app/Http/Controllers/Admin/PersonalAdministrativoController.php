<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use App\Models\PersonalAdministrativo;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PersonalAdministrativoController extends Controller
{
    public function index(Request $request)
    {
        // CU24: Busca por CI, nombre, apellido, cargo o area.
        $search = trim((string) $request->input('search'));
        $personal = PersonalAdministrativo::with('usuario')
            ->when($search, function ($query) use ($search) {
                $query->where('id_secretaria', 'like', "%{$search}%")
                    ->orWhere('nombre', 'like', "%{$search}%")
                    ->orWhere('ap_paterno', 'like', "%{$search}%")
                    ->orWhere('ap_materno', 'like', "%{$search}%")
                    ->orWhere('direccion', 'like', "%{$search}%")
                    ->orWhereHas('usuario', function ($usuarioQuery) use ($search) {
                        $usuarioQuery->where('username', 'like', "%{$search}%");
                    });
                   // ->orWhere('cargo', 'like', "%{$search}%")
                   // ->orWhere('area', 'like', "%{$search}%");
            })
            ->orderBy('ap_paterno')
            ->orderBy('nombre')
            ->get();

        return view('admin.personal_administrativo.index', compact('personal', 'search'));
    }

    public function create()
    {
        return view('admin.personal_administrativo.create');
    }

    public function store(Request $request)
    {
        // CU24 y CU01: Valida datos personales, credenciales y confirmacion de password.
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'ap_paterno' => ['required', 'string', 'max:50'],
            'ap_materno' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:usuario,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // CU24 y CU01: Usa transaccion para crear usuario y personal administrativo juntos.
        DB::transaction(function () use ($data) {
            // CU01: Crea el usuario con rol de personal administrativo/secretaria.
            $usuario = User::create([
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'id_rol' => Rol::SECRETARIA->value,
            ]);

            // CU24: Crea el registro en la tabla secretaria vinculado al usuario.
            PersonalAdministrativo::create([
                'nombre' => $data['nombre'],
                'ap_paterno' => $data['ap_paterno'],
                'ap_materno' => $data['ap_materno'] ?? '',
                'direccion' => $data['direccion'] ?? '',
                'telefono' => $data['telefono'] ?? '',
                'correo' => $data['correo'] ?? '',
                'id_user' => $usuario->id_user,
            ]);
        });

        return redirect()->route('admin.personal-administrativo.index')
            ->with('mensaje', 'Personal administrativo registrado exitosamente.')
            ->with('icono', 'success');
    }

    public function edit(PersonalAdministrativo $personalAdministrativo)
    {
        $personalAdministrativo->load('usuario');

        return view('admin.personal_administrativo.edit', compact('personalAdministrativo'));
    }

    public function update(Request $request, PersonalAdministrativo $personalAdministrativo)
    {
        $personalAdministrativo->load('usuario');

        // CU24 y CU01: Valida datos personales y nombre de usuario editable.
        $data = $this->validarPersonal($request, $personalAdministrativo->id_secretaria);
        $credenciales = $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('usuario', 'username')->ignore(optional($personalAdministrativo->usuario)->id_user, 'id_user'),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        DB::transaction(function () use ($personalAdministrativo, $data, $credenciales) {
            $personalAdministrativo->update($data);

            $usuario = $personalAdministrativo->usuario;

            if (!$usuario) {
                $usuario = User::create([
                    'username' => $credenciales['username'],
                    'password' => Hash::make('secretaria' . $personalAdministrativo->id_secretaria),
                    'id_rol' => Rol::SECRETARIA->value,
                ]);

                $personalAdministrativo->id_user = $usuario->id_user;
                $personalAdministrativo->save();
            } else {
                $usuario->username = $credenciales['username'];
                $usuario->id_rol = Rol::SECRETARIA->value;

                if (!empty($credenciales['password'])) {
                    $usuario->password = Hash::make($credenciales['password']);
                }

                $usuario->save();
            }
        });

        return redirect()->route('admin.personal-administrativo.index')
            ->with('mensaje', 'Personal administrativo actualizado exitosamente.')
            ->with('icono', 'success');
    }

    public function destroy(PersonalAdministrativo $personalAdministrativo)
    {
        // CU24: Si en la BD existen dependencias, se rechaza por integridad referencial.
        try {
            DB::transaction(function () use ($personalAdministrativo) {
                $usuario = $personalAdministrativo->usuario;
                $personalAdministrativo->delete();
                $usuario?->delete();
            });
        } catch (QueryException $e) {
            return redirect()->route('admin.personal-administrativo.index')
                ->with('mensaje', 'No se puede eliminar. El personal administrativo tiene registros asociados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.personal-administrativo.index')
            ->with('mensaje', 'Personal administrativo eliminado exitosamente.')
            ->with('icono', 'success');
    }

    private function validarPersonal(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:50',
            'ap_paterno' => 'required|string|max:50',
            'ap_materno' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
           // 'cargo' => 'required|string|max:50',
           // 'area' => 'required|string|max:50',
            //'fecha_ingreso' => 'required|date',
        ], [
           // 'ci.unique' => 'Ya existe una persona con ese CI.',
            //'cargo.required' => 'El cargo es obligatorio.',
        ]);
    }
}
