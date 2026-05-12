@extends('adminlte::page')

@section('title', 'Fichas Medicas')

@section('content_header')
    <h1><b>CU23 - Gestionar Ficha Medica</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title">Fichas medicas registradas</h3>
            <form action="{{ route('admin.fichas-medicas.index') }}" method="GET" class="form-inline ml-auto">
                <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Buscar estudiante, sangre o alergias..." value="{{ $search }}">
                <button class="btn btn-info btn-sm mr-2"><i class="fas fa-search"></i> Buscar</button>
                <a href="{{ route('admin.fichas-medicas.index') }}" class="btn btn-secondary btn-sm mr-2"><i class="fas fa-list"></i> Todo</a>
                <a href="{{ route('admin.fichas-medicas.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nueva</a>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>CI</th>
                            <th class="text-center">Sangre</th>
                            <th>Alergias</th>
                            <th>Contacto emergencia</th>
                            <th class="text-center" style="width: 260px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fichas as $ficha)
                            @php
                                $alumno = $ficha->alumno;
                                $nombreCompleto = $alumno ? trim($alumno->nombres . ' ' . $alumno->ap_paterno . ' ' . $alumno->ap_materno) : 'Estudiante no encontrado';
                            @endphp
                            <tr>
                                <td>{{ $nombreCompleto }}</td>
                                <td>{{ $alumno->ci ?? 'N/A' }}</td>
                                <td class="text-center"><span class="badge badge-danger">{{ $ficha->tipo_sangre }}</span></td>
                                <td>{{ $ficha->alergias ?: 'Sin alergias registradas' }}</td>
                                <td>{{ $ficha->contacto_emergencia }} - {{ $ficha->telf_emerg }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('admin.fichas-medicas.show', $ficha) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Consultar
                                    </a>
                                    <a href="{{ route('admin.fichas-medicas.edit', $ficha) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-pencil-alt"></i> Editar
                                    </a>
                                    <form action="{{ route('admin.fichas-medicas.destroy', $ficha) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No se encontraron fichas medicas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
