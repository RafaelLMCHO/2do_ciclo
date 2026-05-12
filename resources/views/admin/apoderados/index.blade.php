@extends('adminlte::page')

@section('title', 'Tutores')

@section('content_header')
    <h1><b>CU04 - Gestionar Tutor</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title">Tutores registrados</h3>
            <form action="{{ route('admin.apoderados.index') }}" method="GET" class="form-inline ml-auto">
                <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Buscar por CI, nombre o estudiante..." value="{{ $search }}">
                <button class="btn btn-info btn-sm mr-2"><i class="fas fa-search"></i> Buscar</button>
                <a href="{{ route('admin.apoderados.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo</a>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>CI</th>
                            <th>Tutor</th>
                            <th>Telefono</th>
                            <th>Usuario</th>
                            <th>Estudiantes vinculados</th>
                            <th class="text-center" style="width: 190px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($apoderados as $apoderado)
                            @php
                                $nombreCompleto = trim($apoderado->nombres . ' ' . $apoderado->ap_paterno . ' ' . $apoderado->ap_materno);
                                $usuario = $apoderado->usuarioConsulta();
                            @endphp
                            <tr>
                                <td>{{ $apoderado->id_apoderado }}</td>
                                <td>{{ $apoderado->ci }}</td>
                                <td>{{ $nombreCompleto }}</td>
                                <td>{{ $apoderado->telefono }}</td>
                                <td>{{ $usuario->username ?? 'Sin usuario' }}</td>
                                <td>
                                    @forelse ($apoderado->alumnos as $alumno)
                                        <span class="badge badge-info">
                                            {{ trim($alumno->nombres . ' ' . $alumno->ap_paterno . ' ' . $alumno->ap_materno) }}
                                            ({{ $alumno->pivot->descripcion }})
                                        </span>
                                    @empty
                                        <span class="text-muted">Sin estudiantes</span>
                                    @endforelse
                                </td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('admin.apoderados.edit', $apoderado) }}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i> Editar</a>
                                    <form action="{{ route('admin.apoderados.destroy', $apoderado) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No se encontraron tutores.</td>
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
