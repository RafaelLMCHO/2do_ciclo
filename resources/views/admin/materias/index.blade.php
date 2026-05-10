@extends('adminlte::page')

@section('title', 'Materias')

@section('content_header')
    <h1><b>Materias</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Gestión de Materias</h3>
            <div class="d-flex flex-wrap align-items-center ml-auto gap-2">
                <form action="{{ route('admin.materias.index') }}" method="GET" class="form-inline mb-2 mb-md-0" id="filter-form">
                    <div class="input-group input-group-sm mr-2">
                        <input type="text" name="search" class="form-control" placeholder="Buscar materia..." value="{{ $search ?? '' }}">
                        <span class="input-group-append">
                            <button type="submit" class="btn btn-info btn-flat"><i class="fas fa-search"></i> Buscar</button>
                        </span>
                    </div>

                    <a href="{{ route('admin.materias.index') }}" class="btn btn-secondary btn-sm" title="Limpiar filtros">
                        <i class="fas fa-list"></i> Mostrar todo
                    </a>
                </form>

                <a href="{{ route('admin.materias.create') }}" class="btn btn-primary btn-sm ml-2">
                    <i class="fas fa-plus"></i> Nueva Materia
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">ID</th>
                            <th>Nombre de la Materia</th>
                            <th>Distintivo</th>
                            <th>Campo de Saberes</th>
                            <th style="width: 200px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materias as $materia)
                            <tr>
                                <td class="text-center">{{ $materia->id_materia }}</td>
                                <td>{{ $materia->nombre }}</td>
                                <td>{{ $materia->distintivo }}</td>
                                <td>{{ $materia->campo ? $materia->campo->descripcion : 'N/A' }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('admin.materias.edit', $materia->id_materia) }}" class="btn btn-success btn-sm" title="Editar materia">
                                        <i class="fas fa-pencil-alt"></i> Editar
                                    </a>
                                    <form action="{{ route('admin.materias.destroy', $materia->id_materia) }}" method="POST" id="form-delete-{{ $materia->id_materia }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" title="Eliminar materia" onclick="confirmarEliminar({{ $materia->id_materia }}, '{{ addslashes($materia->nombre) }}')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No se encontraron materias.</td>
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
    function confirmarEliminar(id, nombre) {
        Swal.fire({
            title: '¿Estás seguro?',
            html: `Estás a punto de eliminar la materia <strong>${nombre}</strong>.<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Sí, eliminar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-delete-' + id).submit();
            }
        });
    }

    @if (Session::has('mensaje'))
        Swal.fire({
            icon: "{{ Session::get('icono') }}",
            title: "{{ Session::get('mensaje') }}",
            showConfirmButton: false,
            timer: 4000
        });
    @endif
</script>
@stop
