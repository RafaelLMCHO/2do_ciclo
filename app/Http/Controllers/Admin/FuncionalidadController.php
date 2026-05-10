<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Funcionalidad;
use App\Models\Modulo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FuncionalidadController extends Controller
{
    public function index(Request $request)
    {
        // CU09: Busca por nombre, descripcion o modulo.
        $search = trim((string) $request->input('search'));
        $funcionalidades = Funcionalidad::with('modulo')
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhereHas('modulo', fn ($q) => $q->where('nombre', 'like', "%{$search}%"));
            })
            ->orderBy('nombre')
            ->get();

        return view('admin.funcionalidades.index', compact('funcionalidades', 'search'));
    }

    public function create()
    {
        $modulos = Modulo::orderBy('nombre')->get();
        return view('admin.funcionalidades.create', compact('modulos'));
    }

    public function store(Request $request)
    {
        // CU09: El nombre solo debe ser unico dentro del mismo modulo.
        $data = $request->validate([
            'id_modulo' => 'required|exists:modulos,id_modulo',
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('funcionalidades', 'nombre')->where('id_modulo', $request->input('id_modulo')),
            ],
            'descripcion' => 'nullable|string|max:1000',
        ], [
            'nombre.unique' => 'La funcionalidad ya existe en este modulo.',
        ]);

        Funcionalidad::create($data);

        return redirect()->route('admin.funcionalidades.index')
            ->with('mensaje', 'Funcionalidad registrada exitosamente.')
            ->with('icono', 'success');
    }

    public function edit(Funcionalidad $funcionalidad)
    {
        $modulos = Modulo::orderBy('nombre')->get();
        return view('admin.funcionalidades.edit', compact('funcionalidad', 'modulos'));
    }

    public function update(Request $request, Funcionalidad $funcionalidad)
    {
        $data = $request->validate([
            'id_modulo' => 'required|exists:modulos,id_modulo',
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('funcionalidades', 'nombre')
                    ->where('id_modulo', $request->input('id_modulo'))
                    ->ignore($funcionalidad->id_funcionalidad, 'id_funcionalidad'),
            ],
            'descripcion' => 'nullable|string|max:1000',
        ], [
            'nombre.unique' => 'La funcionalidad ya existe en este modulo.',
        ]);

        $funcionalidad->update($data);

        return redirect()->route('admin.funcionalidades.index')
            ->with('mensaje', 'Funcionalidad actualizada exitosamente.')
            ->with('icono', 'success');
    }

    public function destroy(Funcionalidad $funcionalidad)
    {
        // CU09: La BD bloquea eliminaciones si en el futuro se vincula a roles.
        try {
            $funcionalidad->delete();
        } catch (QueryException $e) {
            return redirect()->route('admin.funcionalidades.index')
                ->with('mensaje', 'No se puede eliminar. La funcionalidad esta asignada a uno o mas roles.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.funcionalidades.index')
            ->with('mensaje', 'Funcionalidad eliminada exitosamente.')
            ->with('icono', 'success');
    }
}
