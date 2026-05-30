@extends('adminlte::page')

@section('title', 'Gestionar Pagos')

@section('content_header')
    <h1><b>Gestionar Pagos</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">CU17: Gestion de pagos</h3>
            <a href="{{ route('admin.pagos.create') }}" class="btn btn-primary btn-sm float-right">
                <i class="fas fa-plus"></i> Registrar Pago
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pagos.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Buscar</label>
                            <input type="text" name="search" class="form-control" placeholder="Estudiante, CI, curso, concepto..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tipo</label>
                            <select name="tipo" class="form-control">
                                <option value="">Todos</option>
                                @foreach($tipos as $item)
                                    <option value="{{ $item }}" {{ (string) $tipo === (string) $item ? 'selected' : '' }}>{{ ucfirst($item) }}</option>
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
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="alert alert-warning py-2 mb-0">
                        Total pendiente: <strong>{{ number_format((float) $totalPendiente, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-success py-2 mb-0">
                        Total pagado: <strong>{{ number_format((float) $totalPagado, 2) }}</strong>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>CI</th>
                            <th>Curso</th>
                            <th>Gestion</th>
                            <th>Concepto</th>
                            <th>Vencimiento</th>
                            <th class="text-right">Monto</th>
                            <th>Estado</th>
                            <th>Pago</th>
                            <th style="width: 280px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                            <tr>
                                <td>{{ $pago->alumno }}</td>
                                <td>{{ $pago->ci_alumno }}</td>
                                <td>{{ $pago->curso }}</td>
                                <td>{{ $pago->gestion }}</td>
                                <td>{{ ucfirst($pago->tipo) }} - {{ $pago->concepto }}</td>
                                <td>{{ \Carbon\Carbon::parse($pago->fecha_vencimiento)->format('d/m/Y') }}</td>
                                <td class="text-right">{{ number_format((float) $pago->monto_pendiente, 2) }}</td>
                                <td>
                                    @php
                                        $badge = match ($pago->estado_pago) {
                                            'Pagado' => 'success',
                                            'Anulado' => 'danger',
                                            default => 'warning',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badge }}">{{ $pago->estado_pago }}</span>
                                    @if($pago->motivo_anulacion)
                                        <br><small class="text-muted">{{ $pago->motivo_anulacion }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($pago->fecha_pago)
                                        {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}<br>
                                        <strong>{{ number_format((float) $pago->monto_pagado, 2) }}</strong>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pago->estado_pago === 'Pagado')
                                        <a href="{{ route('admin.pagos.edit', [$pago->tipo, $pago->id_referencia]) }}" class="btn btn-success btn-sm" title="Editar pago">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('admin.pagos.anular', [$pago->tipo, $pago->id_referencia]) }}" method="POST" class="d-inline-flex align-items-center mt-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="motivo_anulacion" class="form-control form-control-sm mr-1" placeholder="Motivo" required style="width: 150px;">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Anular pago">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.pagos.create', ['tipo' => $pago->tipo, 'referencia' => $pago->id_referencia]) }}" class="btn btn-primary btn-sm" title="Registrar pago">
                                            <i class="fas fa-cash-register"></i> Pagar
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No se encontraron obligaciones o pagos registrados.</td>
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
