@extends('adminlte::page')

@section('title', 'Historial Academico')

@section('content_header')
    <h1>Historial Academico</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>Consulta academica del estudiante</h4>
            <p>Notas por gestion, curso, materia y trimestre</p>
        </div>
        @if($alumnoSeleccionado && $notas->isNotEmpty())
            <div class="list-toolbar">
                <a href="{{ route('admin.historial-academico.imprimir', request()->query()) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
            </div>
        @endif
    </div>

    @include('admin.academico.partials.filtros', ['routeName' => 'admin.historial-academico.index'])

    @if($alumnoSeleccionado)
        @include('admin.academico.partials.resumen-alumno')

        @if($notas->isEmpty())
            <div class="alert alert-warning">
                No existen registros academicos para este estudiante.
            </div>
        @else
            @foreach($notasPorGestion as $gestion => $notasGestion)
                <div class="card" style="overflow: hidden;">
                    <div class="card-header">
                        <strong>{{ $gestion }}</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Curso</th>
                                        <th>Materia</th>
                                        <th>Trimestre</th>
                                        <th class="text-center">SER</th>
                                        <th class="text-center">SABER</th>
                                        <th class="text-center">HACER</th>
                                        <th class="text-center">Autoev.</th>
                                        <th class="text-center">Promedio</th>
                                        <th>Descripcion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notasGestion as $nota)
                                        <tr>
                                            <td>{{ $nota->curso }}</td>
                                            <td>{{ $nota->materia }}</td>
                                            <td><span class="badge-chip">{{ $nota->trimestre }}</span></td>
                                            <td class="text-center">{{ $nota->ser }}</td>
                                            <td class="text-center">{{ $nota->saber }}</td>
                                            <td class="text-center">{{ $nota->hacer }}</td>
                                            <td class="text-center">{{ $nota->autoevaluacion }}</td>
                                            <td class="text-center">
                                                <span class="badge-chip" style="background: #dbeafe; color: #1e40af; font-weight: 700;">
                                                    {{ number_format((float) $nota->promediofinal, 2) }}
                                                </span>
                                            </td>
                                            <td>{{ $nota->descripcion }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @elseif($search)
        <div class="alert alert-warning">Estudiante no encontrado.</div>
    @else
        <div class="alert alert-info">Seleccione o busque un estudiante para consultar su historial academico.</div>
    @endif
@stop
