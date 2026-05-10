@extends('adminlte::page')

@section('content_header')
    <h1><b>Años Escolares</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title"> Gestionar Año Escolar</h3>
            <form action="{{ route('admin.gestiones.index') }}" method="GET" class="form-inline ml-auto">
                <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Buscar anio, fecha o estado..." value="{{ $search ?? '' }}">
                <button class="btn btn-info btn-sm mr-2"><i class="fas fa-search"></i> Buscar</button>
                <a href="{{ route('admin.gestiones.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo</a>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>Año</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Estado</th>
                        <th class="text-center" style="width: 270px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gestiones as $gestion)
                        <tr>
                            <td>{{ $gestion->nombre }}</td>
                            <td>{{ optional($gestion->fechainicio)->format('Y-m-d') }}</td>
                            <td>{{ optional($gestion->fechafin)->format('Y-m-d') }}</td>
                            <td><span class="badge badge-{{ $gestion->activo ? 'success' : 'secondary' }}">{{ $gestion->activo ? 'Activo' : 'Inactivo' }}</span></td>
                            <td class="text-center">
                                <form action="{{ route('admin.gestiones.activar', $gestion->id_gestion) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button class="btn btn-info btn-sm" {{ $gestion->activo ? 'disabled' : '' }}><i class="fas fa-check"></i> Activar</button>
                                </form>
                                <a href="{{ route('admin.gestiones.edit', $gestion->id_gestion) }}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i> Editar</a>
                                <form action="{{ route('admin.gestiones.destroy', $gestion->id_gestion) }}" method="post" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No se encontraron años escolares.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
