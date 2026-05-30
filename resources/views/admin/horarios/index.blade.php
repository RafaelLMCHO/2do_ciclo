@extends('adminlte::page')

@section('title', 'Horarios')

@section('content_header')
    <h1><b>Gestionar Horario</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">CU14: Asignaci&oacute;n de d&iacute;as, horas y aulas</h3>
            <a href="{{ route('admin.horarios.create') }}" class="btn btn-primary btn-sm float-right">
                <i class="fas fa-plus"></i> Nuevo Horario
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.horarios.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Buscar</label>
                            <input type="text" name="search" class="form-control" placeholder="Curso, docente, materia, aula o paralelo..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>D&iacute;a</label>
                            <select name="dia" class="form-control">
                                <option value="">Todos</option>
                                @foreach($dias as $opcion)
                                    <option value="{{ $opcion }}" {{ ($dia ?? '') === $opcion ? 'selected' : '' }}>{{ $opcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list"></i> Mostrar todo
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Paralelo</th>
                            <th>Materia</th>
                            <th>Docente</th>
                            <th>Gesti&oacute;n</th>
                            <th>D&iacute;a</th>
                            <th class="text-center">Inicio</th>
                            <th class="text-center">Fin</th>
                            <th>Aula</th>
                            <th style="width: 180px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($horarios as $horario)
                            <tr>
                                <td>{{ $horario->curso }}</td>
                                <td>{{ $horario->paralelo }}</td>
                                <td>{{ $horario->materia }}</td>
                                <td>{{ $horario->docente }}</td>
                                <td>{{ $horario->gestion }}</td>
                                <td>{{ $horario->dia }}</td>
                                <td class="text-center">{{ substr($horario->hora_inicio, 0, 5) }}</td>
                                <td class="text-center">{{ substr($horario->hora_fin, 0, 5) }}</td>
                                <td>{{ $horario->aula }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('admin.horarios.edit', [$horario->id_materia, $horario->id_gestion, $horario->id_curso, $horario->id_paralelo]) }}" class="btn btn-success btn-sm" title="Editar horario">
                                        <i class="fas fa-pencil-alt"></i> Editar
                                    </a>
                                    <form action="{{ route('admin.horarios.destroy', [$horario->id_materia, $horario->id_gestion, $horario->id_curso, $horario->id_paralelo]) }}" method="POST" id="form-delete-{{ $horario->id_materia }}-{{ $horario->id_gestion }}-{{ $horario->id_curso }}-{{ $horario->id_paralelo }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" title="Eliminar horario" onclick="confirmarEliminar('{{ $horario->id_materia }}-{{ $horario->id_gestion }}-{{ $horario->id_curso }}-{{ $horario->id_paralelo }}', '{{ addslashes($horario->materia) }}')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No se encontraron horarios registrados.</td>
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
    function confirmarEliminar(key, materia) {
        Swal.fire({
            title: 'Estas seguro?',
            html: `Se eliminara el horario de <strong>${materia}</strong>.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Si, eliminar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-delete-' + key).submit();
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
