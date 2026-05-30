@extends('adminlte::page')

@section('title', 'Gestionar Matriculas')

@section('content_header')
    <h1><b>Gestionar Matriculas</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">CU11: Gestion de matriculas</h3>
            <a href="{{ route('admin.matriculas.create') }}" class="btn btn-primary btn-sm float-right">
                <i class="fas fa-plus"></i> Registrar Matricula
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.matriculas.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Buscar</label>
                            <input type="text" name="search" class="form-control" placeholder="Estudiante, CI, curso, tutor..." value="{{ $search ?? '' }}">
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
                            <label>Estado</label>
                            <select name="estado" class="form-control">
                                <option value="">Todos</option>
                                @foreach($estados as $item)
                                    <option value="{{ $item }}" {{ (string) $estado === (string) $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-info btn-block">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.matriculas.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i> Mostrar todo
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>CI</th>
                            <th>Curso</th>
                            <th>Gestion</th>
                            <th>Tutor</th>
                            <th>Fecha</th>
                            <th class="text-right">Monto</th>
                            <th>Estado</th>
                            <th style="width: 220px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matriculas as $matricula)
                            <tr>
                                <td>{{ $matricula->alumno }}</td>
                                <td>{{ $matricula->ci_alumno }}</td>
                                <td>{{ $matricula->curso }}</td>
                                <td>{{ $matricula->gestion }}</td>
                                <td>{{ $matricula->apoderado ?: 'Sin tutor registrado' }}</td>
                                <td>{{ \Carbon\Carbon::parse($matricula->fecha)->format('d/m/Y') }}</td>
                                <td class="text-right">{{ number_format((float) $matricula->monto, 2) }}</td>
                                <td>
                                    <form action="{{ route('admin.matriculas.estado', $matricula->id_inscripcion) }}" method="POST" class="d-flex">
                                        @csrf
                                        @method('PATCH')
                                        <select name="estado" class="form-control form-control-sm mr-1">
                                            @foreach($estados as $item)
                                                <option value="{{ $item }}" {{ $matricula->estado === $item ? 'selected' : '' }}>{{ $item }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-outline-primary btn-sm" title="Cambiar estado">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('admin.matriculas.edit', $matricula->id_inscripcion) }}" class="btn btn-success btn-sm" title="Editar matricula">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('admin.matriculas.destroy', $matricula->id_inscripcion) }}" method="POST" id="form-delete-{{ $matricula->id_inscripcion }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" title="Eliminar matricula" onclick="confirmarEliminar('{{ $matricula->id_inscripcion }}', '{{ addslashes($matricula->alumno) }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No se encontraron matriculas registradas.</td>
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
    function confirmarEliminar(id, alumno) {
        Swal.fire({
            title: 'Estas seguro?',
            html: `Se eliminara la matricula de <strong>${alumno}</strong>.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Si, eliminar',
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
            timer: 3500
        });
    @endif
</script>
@stop
