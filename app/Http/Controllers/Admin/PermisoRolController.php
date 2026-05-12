<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol as RolEnum;
use App\Http\Controllers\Controller;
use App\Models\Funcionalidad;
use App\Models\Modulo;
use App\Models\Rol;
use Illuminate\Http\Request;

// CU09/CU10: Asigna funcionalidades a roles para controlar permisos dinamicos.
class PermisoRolController extends Controller
{
    public function index(Request $request)
    {
        $roles = Rol::orderBy('id_rol')->get();
        $idRol = (int) $request->input('id_rol', RolEnum::SECRETARIA->value);
        $rol = Rol::findOrFail($idRol);

        $modulos = Modulo::with(['funcionalidades' => fn ($query) => $query->orderBy('nombre')])
            ->orderBy('nombre')
            ->get();

        $permisosAsignados = $rol->funcionalidades()
            ->pluck('funcionalidades.id_funcionalidad')
            ->map(fn ($id) => (int) $id)
            ->all();

        return view('admin.permisos.index', compact('roles', 'rol', 'modulos', 'permisosAsignados'));
    }

    public function update(Request $request, Rol $rol)
    {
        if ((int) $rol->id_rol === RolEnum::ADMIN->value) {
            return redirect()->route('admin.permisos.index', ['id_rol' => $rol->id_rol])
                ->with('mensaje', 'El Administrador tiene todos los permisos automaticamente.')
                ->with('icono', 'info');
        }

        $data = $request->validate([
            'funcionalidades' => ['nullable', 'array'],
            'funcionalidades.*' => ['integer', 'exists:funcionalidades,id_funcionalidad'],
        ]);

        $rol->funcionalidades()->sync($data['funcionalidades'] ?? []);

        return redirect()->route('admin.permisos.index', ['id_rol' => $rol->id_rol])
            ->with('mensaje', 'Permisos actualizados exitosamente.')
            ->with('icono', 'success');
    }
}
