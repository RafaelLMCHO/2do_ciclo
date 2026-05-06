@extends('adminlte::page')

@section('title', 'Cursos')

@section('content_header')
    <h1><b>Cursos</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Gestión de Cursos</h3>
            <div class="d-flex align-items-center ml-auto">
                <form action="{{ route('admin.cursos.index') }}" method="GET" class="form-inline mr-3">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Buscar curso..." value="{{ $search ?? '' }}">
                        <span class="input-group-append">
                            <button type="submit" class="btn btn-info btn-flat"><i class="fas fa-search"></i> Buscar</button>
                        </span>
                    </div>
                </form>
                <a href="{{ route('admin.cursos.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Curso
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">ID</th>
                            <th>Nombre del Curso</th>
                            <th style="width: 200px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cursos as $curso)
                            <tr>
                                <td class="text-center">{{ $curso->id_curso }}</td>
                                <td>{{ $curso->nombre }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('admin.cursos.edit', $curso->id_curso) }}" class="btn btn-success btn-sm" title="Editar curso">
                                        <i class="fas fa-pencil-alt"></i> Editar
                                    </a>
                                    <form action="{{ route('admin.cursos.destroy', $curso->id_curso) }}" method="POST" id="form-delete-{{ $curso->id_curso }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" title="Eliminar curso" onclick="confirmarEliminar({{ $curso->id_curso }}, '{{ addslashes($curso->nombre) }}')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No se encontraron cursos.</td>
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
            html: `Estás a punto de eliminar el curso <strong>${nombre}</strong>.<br>Esta acción no se puede deshacer.`,
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
