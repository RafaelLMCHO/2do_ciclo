@extends('adminlte::page')

@section('title', 'Gestionar Notas')

@section('content_header')
    <h1><b>Gestionar Notas</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">CU15: Gesti&oacute;n de calificaciones</h3>
            <a href="{{ route('admin.notas.create') }}" class="btn btn-primary btn-sm float-right">
                <i class="fas fa-plus"></i> Registrar Nota
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.notas.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Buscar</label>
                            <input type="text" name="search" class="form-control" placeholder="Estudiante, materia, curso..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Gesti&oacute;n</label>
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
                            <label>Materia</label>
                            <select name="id_materia" class="form-control">
                                <option value="">Todas</option>
                                @foreach($materias as $materia)
                                    <option value="{{ $materia->id_materia }}" {{ (string) $idMateria === (string) $materia->id_materia ? 'selected' : '' }}>
                                        {{ $materia->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Trimestre</label>
                            <select name="id_trimestre" class="form-control">
                                <option value="">Todos</option>
                                @foreach($trimestres as $trimestre)
                                    <option value="{{ $trimestre->id_trimestre }}" {{ (string) $idTrimestre === (string) $trimestre->id_trimestre ? 'selected' : '' }}>
                                        Trimestre {{ $trimestre->id_trimestre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-info btn-block" title="Buscar notas">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.notas.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i> Mostrar todo
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Curso</th>
                            <th>Materia</th>
                            <th>Gesti&oacute;n</th>
                            <th>Trimestre</th>
                            <th class="text-center">Ser</th>
                            <th class="text-center">Saber</th>
                            <th class="text-center">Hacer</th>
                            <th class="text-center">Autoev.</th>
                            <th class="text-center">Promedio</th>
                            <th style="width: 180px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notas as $nota)
                            <tr>
                                <td>{{ $nota->alumno }}</td>
                                <td>{{ $nota->curso }}</td>
                                <td>{{ $nota->materia }}</td>
                                <td>{{ $nota->gestion }}</td>
                                <td>{{ $nota->trimestre }}</td>
                                <td class="text-center">{{ $nota->ser }}</td>
                                <td class="text-center">{{ $nota->saber }}</td>
                                <td class="text-center">{{ $nota->hacer }}</td>
                                <td class="text-center">{{ $nota->autoevaluacion }}</td>
                                <td class="text-center">
                                    <span class="badge badge-primary">{{ number_format($nota->promediofinal, 2) }}</span>
                                </td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('admin.notas.edit', [$nota->id_alumno, $nota->id_materia, $nota->id_gestion, $nota->id_curso, $nota->id_trimestre]) }}" class="btn btn-success btn-sm" title="Editar nota">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('admin.notas.destroy', [$nota->id_alumno, $nota->id_materia, $nota->id_gestion, $nota->id_curso, $nota->id_trimestre]) }}" method="POST" id="form-delete-{{ $nota->id_alumno }}-{{ $nota->id_materia }}-{{ $nota->id_gestion }}-{{ $nota->id_curso }}-{{ $nota->id_trimestre }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" title="Eliminar nota" onclick="confirmarEliminar('{{ $nota->id_alumno }}-{{ $nota->id_materia }}-{{ $nota->id_gestion }}-{{ $nota->id_curso }}-{{ $nota->id_trimestre }}', '{{ addslashes($nota->alumno) }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">No se encontraron notas registradas.</td>
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
    function confirmarEliminar(key, alumno) {
        Swal.fire({
            title: 'Estas seguro?',
            html: `Se eliminara la nota registrada para <strong>${alumno}</strong>.`,
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
