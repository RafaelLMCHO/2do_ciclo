@extends('adminlte::page')

@section('title', 'Rendimiento Academico')

@section('content_header')
    <h1>Rendimiento Academico</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>Seguimiento de rendimiento</h4>
            <p>Promedio general, materias con bajo rendimiento y asistencia</p>
        </div>
        @if($alumnoSeleccionado && ($notas->isNotEmpty() || ($asistencia['total'] ?? 0) > 0))
            <div class="list-toolbar">
                <a href="{{ route('admin.rendimiento-academico.imprimir', request()->query()) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
            </div>
        @endif
    </div>

    @include('admin.academico.partials.filtros', ['routeName' => 'admin.rendimiento-academico.index'])

    @if($alumnoSeleccionado)
        @include('admin.academico.partials.resumen-alumno')

        <div class="stats-grid" style="margin-bottom: 1.5rem;">
            <div class="stat-card {{ $materiasBajoRendimiento->isEmpty() ? 'green' : 'orange' }}">
                <div class="stat-icon {{ $materiasBajoRendimiento->isEmpty() ? 'si-green' : 'si-orange' }}"><i class="fas fa-book-reader"></i></div>
                <div class="stat-number">{{ $materiasBajoRendimiento->count() }}</div>
                <div class="stat-label">Materias con bajo rendimiento</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon si-green"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-number">{{ $asistencia['porcentaje'] !== null ? number_format((float) $asistencia['porcentaje'], 2).'%' : '-' }}</div>
                <div class="stat-label">Asistencia</div>
            </div>
        </div>

        @if($notas->isEmpty())
            <div class="alert alert-warning">No existen notas registradas para calcular el rendimiento academico.</div>
        @else
            <div class="card" style="overflow: hidden;">
                <div class="card-header"><strong>Rendimiento por materia</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Gestion</th>
                                    <th>Curso</th>
                                    <th>Materia</th>
                                    <th class="text-center">Promedio</th>
                                    <th class="text-center">Mejor</th>
                                    <th class="text-center">Menor</th>
                                    <th class="text-center">Registros</th>
                                    <th>Situacion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rendimientoMaterias as $materia)
                                    <tr>
                                        <td>{{ $materia->gestion }}</td>
                                        <td>{{ $materia->curso }}</td>
                                        <td>{{ $materia->materia }}</td>
                                        <td class="text-center">{{ number_format((float) $materia->promedio, 2) }}</td>
                                        <td class="text-center">{{ number_format((float) $materia->mejor, 2) }}</td>
                                        <td class="text-center">{{ number_format((float) $materia->menor, 2) }}</td>
                                        <td class="text-center">{{ $materia->registros }}</td>
                                        <td>
                                            @if((float) $materia->promedio >= 51)
                                                <span class="status-badge on"><span class="status-dot"></span>Regular</span>
                                            @else
                                                <span class="status-badge off"><span class="status-dot"></span>Bajo rendimiento</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if(($asistencia['total'] ?? 0) === 0)
            <div class="alert alert-info">El estudiante no tiene registros de asistencia para los filtros seleccionados.</div>
        @endif
    @elseif($search)
        <div class="alert alert-warning">Estudiante no encontrado.</div>
    @else
        <div class="alert alert-info">Seleccione o busque un estudiante para consultar su rendimiento academico.</div>
    @endif
@stop
