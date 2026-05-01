<?php

namespace App\Http\Controllers\Admin;

use App\Domain\GestionAcademica\Services\GestionAcademicaService;
use App\Http\Controllers\Controller;
use App\Models\Nivel;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    public function __construct(
        protected GestionAcademicaService $service,
    ) {}

    public function index()
    {
        $niveles = $this->service->todosLosNiveles();
        return view('admin.niveles.index', compact('niveles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:255|unique:nivels',
        ]);

        $this->service->crearNivel($request->nombre);

        return redirect()->route('admin.nivels.index')
            ->with('mensaje', 'Nivel creado exitosamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:255|unique:nivels,nombre,'.$id,
        ]);

        $nivel = Nivel::findOrFail($id);
        $this->service->actualizarNivel($nivel, $request->nombre);

        return redirect()->route('admin.nivels.index')
            ->with('mensaje', 'Nivel actualizado exitosamente.')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        try {
            $nivel = Nivel::findOrFail($id);
            $this->service->eliminarNivel($nivel);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.nivels.index')
                ->with('mensaje', 'No se puede eliminar el nivel porque tiene registros relacionados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.nivels.index')
            ->with('mensaje', 'Nivel eliminado exitosamente.')
            ->with('icono', 'success');
    }
}
