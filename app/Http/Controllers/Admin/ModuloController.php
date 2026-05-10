<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ModuloController extends Controller
{
    public function index(Request $request)
    {
        // CU10: Buscar modulos por nombre o descripcion, tal como indica el documento.
        $search = trim((string) $request->input('search'));
        $modulos = Modulo::withCount('funcionalidades')
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->get();

        return view('admin.modulos.index', compact('modulos', 'search'));
    }

    public function create()
    {
        return view('admin.modulos.create');
    }

    public function store(Request $request)
    {
        // CU10: Evita nombres duplicados antes de guardar el modulo.
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:modulos,nombre',
            'descripcion' => 'nullable|string|max:1000',
        ], [
            'nombre.unique' => 'El modulo ya existe.',
        ]);

        Modulo::create($data);

        return redirect()->route('admin.modulos.index')
            ->with('mensaje', 'Modulo registrado exitosamente.')
            ->with('icono', 'success');
    }

    public function edit(Modulo $modulo)
    {
        return view('admin.modulos.edit', compact('modulo'));
    }

    public function update(Request $request, Modulo $modulo)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:modulos,nombre,' . $modulo->id_modulo . ',id_modulo',
            'descripcion' => 'nullable|string|max:1000',
        ], [
            'nombre.unique' => 'El modulo ya existe.',
        ]);

        $modulo->update($data);

        return redirect()->route('admin.modulos.index')
            ->with('mensaje', 'Modulo actualizado exitosamente.')
            ->with('icono', 'success');
    }

    public function destroy(Modulo $modulo)
    {
        // CU10: No elimina modulos con funcionalidades asociadas.
        if ($modulo->funcionalidades()->exists()) {
            return redirect()->route('admin.modulos.index')
                ->with('mensaje', 'No se puede eliminar. El modulo tiene funcionalidades registradas.')
                ->with('icono', 'error');
        }

        try {
            $modulo->delete();
        } catch (QueryException $e) {
            return redirect()->route('admin.modulos.index')
                ->with('mensaje', 'No se puede eliminar el modulo porque tiene registros relacionados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.modulos.index')
            ->with('mensaje', 'Modulo eliminado exitosamente.')
            ->with('icono', 'success');
    }
}
