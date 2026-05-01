<?php

namespace App\Http\Controllers\Admin;

use App\Domain\GestionAcademica\Services\GestionAcademicaService;
use App\Http\Controllers\Controller;
use App\Models\Turno;
use Illuminate\Http\Request;

class TurnoController extends Controller
{
    public function __construct(
        protected GestionAcademicaService $service,
    ) {}

    public function index()
    {
        $turnos = $this->service->todosLosTurnos();
        return view('admin.turnos.index', compact('turnos'));
    }

    public function create()
    {
        return view('admin.turnos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:255|unique:turnos',
        ]);

        $this->service->crearTurno($request->nombre);

        return redirect()->route('admin.turnos.index')
            ->with('mensaje', 'Turno creado exitosamente.')
            ->with('icono', 'success');
    }

    public function edit($id)
    {
        $turno = Turno::find($id);

        if (!$turno) {
            return redirect()->route('admin.turnos.index')
                ->with('mensaje', 'Turno no encontrado.')
                ->with('icono', 'error');
        }

        return view('admin.turnos.edit', compact('turno'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:255|unique:turnos,nombre,'.$id,
        ]);

        $turno = Turno::findOrFail($id);
        $this->service->actualizarTurno($turno, $request->nombre);

        return redirect()->route('admin.turnos.index')
            ->with('mensaje', 'Turno actualizado exitosamente.')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        try {
            $turno = Turno::findOrFail($id);
            $this->service->eliminarTurno($turno);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.turnos.index')
                ->with('mensaje', 'No se puede eliminar el turno porque tiene registros relacionados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.turnos.index')
            ->with('mensaje', 'Turno eliminado exitosamente.')
            ->with('icono', 'success');
    }
}
