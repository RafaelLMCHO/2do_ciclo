@extends('adminlte::page')

@section('title', 'Funcionalidades')

@section('content_header')
    <h1><b>Funcionalidades</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title">Gestionar Funcionalidad</h3>
            <form action="{{ route('admin.funcionalidades.index') }}" method="GET" class="form-inline ml-auto">
                <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Buscar funcionalidad..." value="{{ $search }}">
                <button class="btn btn-info btn-sm mr-2"><i class="fas fa-search"></i> Buscar</button>
                <a href="{{ route('admin.funcionalidades.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo</a>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Modulo</th>
                        <th>Descripcion</th>
                        <th class="text-center" style="width: 190px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($funcionalidades as $funcionalidad)
                        <tr>
                            <td>{{ $funcionalidad->nombre }}</td>
                            <td>{{ $funcionalidad->modulo->nombre }}</td>
                            <td>{{ $funcionalidad->descripcion }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.funcionalidades.edit', $funcionalidad) }}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i> Editar</a>
                                <form action="{{ route('admin.funcionalidades.destroy', $funcionalidad) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">No se encontraron funcionalidades.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
