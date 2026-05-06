<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $cursos = Curso::where('nombre', 'LIKE', '%' . $search . '%')->get();
        } else {
            $cursos = Curso::all();
        }
        
        return view('admin.cursos.index', compact('cursos', 'search'));
    }

    public function create()
    {
        return view('admin.cursos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:curso,nombre',
        ], [
            'nombre.required' => 'El nombre del curso es obligatorio.',
            'nombre.unique' => 'Ya existe un curso con este nombre.',
        ]);

        Curso::create([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('admin.cursos.index')
            ->with('mensaje', 'Curso creado con éxito')
            ->with('icono', 'success');
    }

    public function edit($id)
    {
        $curso = Curso::findOrFail($id);
        return view('admin.cursos.edit', compact('curso'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:curso,nombre,' . $id . ',id_curso',
        ], [
            'nombre.required' => 'El nombre del curso es obligatorio.',
            'nombre.unique' => 'Ya existe un curso con este nombre.',
        ]);

        $curso = Curso::findOrFail($id);
        $curso->update([
            'nombre' => $request->nombre,
        ]);

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
}
