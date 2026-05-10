@extends('adminlte::page')

@section('title', 'Modulos')

@section('content_header')
    <h1><b>Modulos</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title">Gestionar Modulo</h3>
            <form action="{{ route('admin.modulos.index') }}" method="GET" class="form-inline ml-auto">
                <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Buscar modulo..." value="{{ $search }}">
                <button class="btn btn-info btn-sm mr-2"><i class="fas fa-search"></i> Buscar</button>
                <a href="{{ route('admin.modulos.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo</a>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th class="text-center">Funcionalidades</th>
                        <th class="text-center" style="width: 190px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modulos as $modulo)
                        <tr>
                            <td>{{ $modulo->nombre }}</td>
                            <td>{{ $modulo->descripcion }}</td>
                            <td class="text-center">{{ $modulo->funcionalidades_count }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.modulos.edit', $modulo) }}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i> Editar</a>
                                <form action="{{ route('admin.modulos.destroy', $modulo) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">No se encontraron modulos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
