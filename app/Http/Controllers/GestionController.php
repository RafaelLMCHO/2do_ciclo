<?php

namespace App\Http\Controllers;

use App\Domain\GestionAcademica\Services\GestionAcademicaService;
use App\Models\Gestion;
use Illuminate\Http\Request;

class GestionController extends Controller
{
    public function __construct(
        protected GestionAcademicaService $service,
    ) {}

    public function index(Request $request)
    {
        // CU22: Permite buscar por anio, fechas o estado.
        $search = trim((string) $request->input('search'));
        $gestiones = Gestion::query()
            ->when($search, function ($query) use ($search) {
                $estado = strtolower($search);
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('fechainicio', 'like', "%{$search}%")
                    ->orWhere('fechafin', 'like', "%{$search}%");

                if ($estado === 'activo') {
                    $query->orWhere('activo', true);
                }

                if ($estado === 'inactivo') {
                    $query->orWhere('activo', false);
                }
            })
            ->orderByDesc('nombre')
            ->get();

        return view('admin.gestiones.index', compact('gestiones', 'search'));
    }

    public function create()
    {
        return view('admin.gestiones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:255|unique:gestions',
            'fechainicio' => 'required|date',
            'fechafin' => 'required|date|after:fechainicio',
        ]);

        $this->service->crearGestion($request->nombre, $request->fechainicio, $request->fechafin);

        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'Gestion creada exitosamente.')
            ->with('icono', 'success');
    }

    public function show(Gestion $gestion)
    {
        //
    }

    public function edit($id)
    {
        $gestion = Gestion::find($id);

        if (!$gestion) {
            return redirect()->route('admin.gestiones.index')
                ->with('mensaje', 'Gestion no encontrada.')
                ->with('icono', 'error');
        }

        return view('admin.gestiones.edit', compact('gestion'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:255|unique:gestions,nombre,'.$id,
            'fechainicio' => 'required|date',
            'fechafin' => 'required|date|after:fechainicio',
        ]);

        $gestion = Gestion::findOrFail($id);
        $this->service->actualizarGestion($gestion, $request->nombre, $request->fechainicio, $request->fechafin);

        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'Gestion actualizada exitosamente.')
            ->with('icono', 'success');
    }

    public function activar($id)
    {
        $gestion = Gestion::findOrFail($id);
        $this->service->activarGestion($gestion);

        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'Gestion activada exitosamente.')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        try {
            $gestion = Gestion::findOrFail($id);
            $this->service->eliminarGestion($gestion);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.gestiones.index')
                ->with('mensaje', 'No se puede eliminar la gestion porque tiene registros relacionados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'Gestion eliminada exitosamente.')
            ->with('icono', 'success');
    }
}
