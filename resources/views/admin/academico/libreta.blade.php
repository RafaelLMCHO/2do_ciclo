@extends('adminlte::page')

@section('title', 'Libreta Academica')

@section('content_header')
    <h1>Libreta Academica</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>Libreta con validacion de pagos</h4>
            <p>Generacion bloqueada cuando existen mensualidades pendientes</p>
        </div>
        @if($alumnoSeleccionado && $notas->isNotEmpty() && $pagos['habilitada'] && ! $esProfesor)
            <div class="list-toolbar">
                <a href="{{ route('admin.libretas.imprimir', request()->query()) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
            </div>
        @endif
    </div>

    @include('admin.academico.partials.filtros', ['routeName' => 'admin.libretas.index'])

    @if($alumnoSeleccionado)
        @include('admin.academico.partials.resumen-alumno')

        @if(! $pagos['registrados'])
            <div class="alert alert-danger">
                No existen mensualidades registradas para validar la libreta academica.
            </div>
        @elseif(! $pagos['habilitada'])
            <div class="alert alert-danger">
                El estudiante tiene pagos pendientes. No se puede imprimir ni descargar la libreta hasta regularizar los pagos.
            </div>
            <div class="card" style="overflow: hidden;">
                <div class="card-header"><strong>Detalle de pagos pendientes</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Gestion</th>
                                    <th>Curso</th>
                                    <th>Mes</th>
                                    <th>Vencimiento</th>
                                    <th>Estado</th>
                                    <th class="text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagos['pendientes'] as $pago)
                                    <tr>
                                        <td>{{ $pago->gestion }}</td>
                                        <td>{{ $pago->curso }}</td>
                                        <td>{{ $pago->mes }}</td>
                                        <td>{{ $pago->fecha ? \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') : 'Sin registro' }}</td>
                                        <td><span class="status-badge off">{{ $pago->estado }}</span></td>
                                        <td class="text-right">
                                            {{ $pago->monto !== null ? 'Bs. ' . number_format((float) $pago->monto, 2) : 'No registrado' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">Total pendiente</th>
                                    <th class="text-right">Bs. {{ number_format((float) $pagos['totalPendiente'], 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-success">
                El estudiante se encuentra al dia. La libreta esta habilitada para generar PDF.
            </div>
        @endif

        @if($notas->isEmpty())
            <div class="alert alert-warning">No existen notas registradas para generar la libreta.</div>
        @else
            <div class="card" style="overflow: hidden;">
                <div class="card-header"><strong>Calificaciones de la libreta</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Gestion</th>
                                    <th>Curso</th>
                                    <th>Materia</th>
                                    <th>Trimestre</th>
                                    <th class="text-center">Promedio</th>
                                    <th>Situacion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notas as $nota)
                                    <tr>
                                        <td>{{ $nota->gestion }}</td>
                                        <td>{{ $nota->curso }}</td>
                                        <td>{{ $nota->materia }}</td>
                                        <td><span class="badge-chip">{{ $nota->trimestre }}</span></td>
                                        <td class="text-center">{{ number_format((float) $nota->promediofinal, 2) }}</td>
                                        <td>
                                            @if((float) $nota->promediofinal >= 51)
                                                <span class="status-badge on"><span class="status-dot"></span>Aprobado</span>
                                            @else
                                                <span class="status-badge off"><span class="status-dot"></span>Reprobado</span>
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
    @elseif($search)
        <div class="alert alert-warning">Estudiante no encontrado.</div>
    @else
        <div class="alert alert-info">Seleccione un estudiante, gestion y trimestre para generar la libreta academica.</div>
    @endif
@stop
