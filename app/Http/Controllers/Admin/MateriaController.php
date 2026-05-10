<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use App\Models\CampoSaberes;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $query = Materia::with('campo');

        if ($search) {
            // CU13: Busca materias por nombre o carga horaria.
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nombre) LIKE LOWER(?)', ['%' . $search . '%'])
                  ->orWhere('carga_horaria', 'like', '%' . $search . '%')
                  ->orWhereRaw('LOWER(distintivo) LIKE LOWER(?)', ['%' . $search . '%']);
            });
        }

        $materias = $query->orderBy('nombre', 'asc')->get();

        return view('admin.materias.index', compact('materias', 'search'));
    }

    public function create()
    {
        $campos = CampoSaberes::all();
        return view('admin.materias.create', compact('campos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:materia,nombre',
            'carga_horaria' => 'required|integer|min:1|max:80',
            'distintivo' => 'nullable|string|max:255',
            'id_campo' => 'required|exists:campos_saberes_conocimientos,id_campo',
        ], [
            'nombre.required' => 'El nombre de la materia es obligatorio.',
            'nombre.unique' => 'Ya existe una materia con este nombre.',
            'id_campo.required' => 'El campo de saberes es obligatorio.',
            'id_campo.exists' => 'El campo de saberes seleccionado no es válido.',
        ]);

        Materia::create([
            'nombre' => $request->nombre,
            'carga_horaria' => $request->carga_horaria,
            'distintivo' => $request->distintivo,
            'id_campo' => $request->id_campo,
        ]);

        return redirect()->route('admin.materias.index')
            ->with('mensaje', 'Materia creada con éxito')
            ->with('icono', 'success');
    }

    public function edit($id)
    {
        $materia = Materia::findOrFail($id);
        $campos = CampoSaberes::all();
        return view('admin.materias.edit', compact('materia', 'campos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:materia,nombre,' . $id . ',id_materia',
            /* 'carga_horaria' => 'required|integer|min:1|max:80', */
            'distintivo' => 'nullable|string|max:255',
            'id_campo' => 'required|exists:campos_saberes_conocimientos,id_campo',
        ], [
            'nombre.required' => 'El nombre de la materia es obligatorio.',
            'nombre.unique' => 'Ya existe una materia con este nombre.',
            'id_campo.required' => 'El campo de saberes es obligatorio.',
            'id_campo.exists' => 'El campo de saberes seleccionado no es válido.',
        ]);

        $materia = Materia::findOrFail($id);
        $materia->update([
            'nombre' => $request->nombre,
            /* 'carga_horaria' => $request->carga_horaria, */
            'distintivo' => $request->distintivo,
            'id_campo' => $request->id_campo,
        ]);

        return redirect()->route('admin.materias.index')
            ->with('mensaje', 'Materia actualizada con éxito')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $materia = Materia::findOrFail($id);
        try {
            $materia->delete();
            return redirect()->route('admin.materias.index')
                ->with('mensaje', 'Materia eliminada con éxito')
                ->with('icono', 'success');
        } catch (\Exception $e) {
            return redirect()->route('admin.materias.index')
                ->with('mensaje', 'No se puede eliminar la materia porque tiene registros relacionados.')
                ->with('icono', 'error');
        }
    }
}
