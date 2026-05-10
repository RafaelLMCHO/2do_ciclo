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

class PersonalAdministrativoController extends Controller
{
    public function index(Request $request)
    {
        // CU24: Busca por CI, nombre, apellido, cargo o area.
        $search = trim((string) $request->input('search'));
        $personal = PersonalAdministrativo::with('usuario')
            ->when($search, function ($query) use ($search) {
                $query->where('ci', 'like', "%{$search}%")
                    ->orWhere('nombre', 'like', "%{$search}%")
                    ->orWhere('ap_paterno', 'like', "%{$search}%")
                    ->orWhere('ap_materno', 'like', "%{$search}%")
                    ->orWhere('cargo', 'like', "%{$search}%")
                    ->orWhere('area', 'like', "%{$search}%");
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
        $data = $this->validarPersonal($request);

        // CU24: Crea el personal y genera automaticamente su usuario.
        $passwordPlano = strtolower(substr($data['nombre'], 0, 3)) . $data['ci'];
        DB::transaction(function () use ($data, $passwordPlano) {
            $usuario = User::create([
                'username' => $this->generarUsername($data['nombre'], $data['ap_paterno']),
                'password' => Hash::make($passwordPlano),
                'id_rol' => Rol::SECRETARIA->value,
            ]);

            PersonalAdministrativo::create($data + ['id_user' => $usuario->id_user]);
        });

        return redirect()->route('admin.personal-administrativo.index')
            ->with('mensaje', 'Personal administrativo registrado exitosamente. Usuario y contrasena generados: ' . $passwordPlano)
            ->with('icono', 'success');
    }

    public function edit(PersonalAdministrativo $personalAdministrativo)
    {
        return view('admin.personal_administrativo.edit', compact('personalAdministrativo'));
    }

    public function update(Request $request, PersonalAdministrativo $personalAdministrativo)
    {
        $data = $this->validarPersonal($request, $personalAdministrativo->id_personal_administrativo);
        $personalAdministrativo->update($data);

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
            'ci' => 'required|string|max:20|unique:personal_administrativo,ci,' . $id . ',id_personal_administrativo',
            'nombre' => 'required|string|max:50',
            'ap_paterno' => 'required|string|max:50',
            'ap_materno' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'cargo' => 'required|string|max:50',
            'area' => 'required|string|max:50',
            'fecha_ingreso' => 'required|date',
        ], [
            'ci.unique' => 'Ya existe una persona con ese CI.',
            'cargo.required' => 'El cargo es obligatorio.',
        ]);
    }

    private function generarUsername(string $nombre, string $apellido): string
    {
        $base = strtolower(preg_replace('/[^a-z0-9]/i', '', substr($nombre, 0, 1) . $apellido));
        $username = $base ?: 'administrativo';
        $contador = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $contador++;
        }

        return $username;
    }
}
