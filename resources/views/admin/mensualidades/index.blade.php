@extends('adminlte::page')

@section('title', 'Gestionar Mensualidades')

@section('content_header')
    <h1><b>Gestionar Mensualidades</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">CU18: Gestion de mensualidades</h3>
            <a href="{{ route('admin.mensualidades.create') }}" class="btn btn-primary btn-sm float-right">
                <i class="fas fa-plus"></i> Generar Mensualidades
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.mensualidades.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Buscar</label>
                            <input type="text" name="search" class="form-control" placeholder="Estudiante, CI, curso..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Gestion</label>
                            <select name="id_gestion" class="form-control">
                                <option value="">Todas</option>
                                @foreach($gestiones as $gestion)
                                    <option value="{{ $gestion->id_gestion }}" {{ (string) $idGestion === (string) $gestion->id_gestion ? 'selected' : '' }}>
                                        {{ $gestion->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Curso</label>
                            <select name="id_curso" class="form-control">
                                <option value="">Todos</option>
                                @foreach($cursos as $curso)
                                    <option value="{{ $curso->id_curso }}" {{ (string) $idCurso === (string) $curso->id_curso ? 'selected' : '' }}>
                                        {{ $curso->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Mes</label>
                            <select name="mes" class="form-control">
                                <option value="">Todos</option>
                                @foreach($meses as $item)
                                    <option value="{{ $item }}" {{ (string) $mes === (string) $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estado" class="form-control">
                                <option value="">Todos</option>
                                @foreach($estados as $item)
                                    <option value="{{ $item }}" {{ (string) $estado === (string) $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-info btn-block" title="Buscar">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.mensualidades.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i> Mostrar todo
                </a>
            </form>

            <div class="alert alert-info py-2">
                Total pendiente segun filtros: <strong>{{ number_format((float) $totalPendiente, 2) }}</strong>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>CI</th>
                            <th>Curso</th>
                            <th>Gestion</th>
                            <th>Mes</th>
                            <th>Vencimiento</th>
                            <th class="text-right">Monto</th>
                            <th class="text-right">Desc.</th>
                            <th>Estado</th>
                            <th>Pago</th>
                            <th style="width: 260px;" class="text-center">Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mensualidades as $mensualidad)
                            <tr>
                                <td>{{ $mensualidad->alumno }}</td>
                                <td>{{ $mensualidad->ci_alumno }}</td>
                                <td>{{ $mensualidad->curso }}</td>
                                <td>{{ $mensualidad->gestion }}</td>
                                <td>{{ $mensualidad->mes }}</td>
                                <td>{{ \Carbon\Carbon::parse($mensualidad->fecha)->format('d/m/Y') }}</td>
                                <td class="text-right">{{ number_format((float) $mensualidad->monto, 2) }}</td>
                                <td class="text-right">{{ number_format((float) $mensualidad->descuento, 2) }}</td>
                                <td>
                                    @php
                                        $badge = match ($mensualidad->estado) {
                                            'Pagado' => 'success',
                                            'Vencido' => 'danger',
                                            default => 'warning',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badge }}">{{ $mensualidad->estado }}</span>
                                </td>
                                <td>
                                    @if($mensualidad->fecha_pago)
                                        {{ \Carbon\Carbon::parse($mensualidad->fecha_pago)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($mensualidad->estado === 'Pagado')
                                        <span class="text-muted">Completado</span>
                                    @else
                                        <form action="{{ route('admin.mensualidades.pago', $mensualidad->id_pago_mensual) }}" method="POST" class="form-inline justify-content-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="monto_recibido" step="0.01" min="0.01" class="form-control form-control-sm mr-1" value="{{ number_format((float) $mensualidad->monto, 2, '.', '') }}" style="width: 105px;" title="Monto recibido">
                                            <input type="date" name="fecha_pago" class="form-control form-control-sm mr-1" value="{{ now()->toDateString() }}" title="Fecha de pago">
                                            <button type="submit" class="btn btn-success btn-sm" title="Registrar pago">
                                                <i class="fas fa-cash-register"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">No se encontraron mensualidades registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    @if (Session::has('mensaje'))
        Swal.fire({
            icon: "{{ Session::get('icono') }}",
            title: "{{ Session::get('mensaje') }}",
            showConfirmButton: false,
            timer: 3500
        });
    @endif
</script>
@stop
