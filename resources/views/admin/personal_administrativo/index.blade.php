@extends('adminlte::page')

@section('title', 'Personal Administrativo')

@section('content_header')
    <h1><b>Personal Administrativo</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title">CU24: Gestionar Personal Administrativo</h3>
            <form action="{{ route('admin.personal-administrativo.index') }}" method="GET" class="form-inline ml-auto">
                <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Buscar por nombre, usuario o direccion..." value="{{ $search }}">
                <button class="btn btn-info btn-sm mr-2"><i class="fas fa-search"></i> Buscar</button>
                <a href="{{ route('admin.personal-administrativo.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo</a>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Nombre de Usuario</th>
                        <th>Correo</th>
                        <th>Direccion</th>
                        <th class="text-center" style="width: 190px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($personal as $item)
                        <tr>
                            <td>{{ $item->id_secretaria }}</td>
                            <td>{{ $item->nombre }} {{ $item->ap_paterno }} {{ $item->ap_materno }}</td>
                            <td>{{ $item->usuario->username ?? 'Sin usuario' }}</td>
                            <td>{{ $item->correo ?? 'Sin correo' }}</td>
                            <td>{{ $item->direccion ?? 'Sin direccion' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.personal-administrativo.edit', $item) }}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i> Editar</a>
                                <form action="{{ route('admin.personal-administrativo.destroy', $item) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No se encontro personal administrativo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
