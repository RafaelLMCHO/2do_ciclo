@extends('adminlte::page')

@section('title', 'Alumnos')

@section('content_header')
    <h1><b>Listado de Alumnos</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Alumnos registrados</h3>
            <div class="card-tools">
                <a href="{{ route('admin.alumnos.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Crear Nuevo Alumno
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>CI</th>
                            <th>Alumno</th>
                            <th>Genero</th>
                            <th>Fecha Nac.</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alumnos as $alumno)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $alumno->ci }}</td>
                                <td>{{ trim($alumno->nombres . ' ' . $alumno->ap_paterno . ' ' . $alumno->ap_materno) }}</td>
                                <td>{{ $alumno->genero === 'F' ? 'Femenino' : 'Masculino' }}</td>
                                <td>{{ $alumno->fecha_nac }}</td>
                                <td>{{ optional($alumno->usuario)->username ?? 'Sin usuario' }}</td>
                                <td>
                                    <div class="d-flex justify-content-center" style="gap: 8px;">
                                        <a href="{{ route('admin.alumnos.edit', $alumno->id_alumno) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-pencil-alt"></i> Editar
                                        </a>
                                        <form action="{{ route('admin.alumnos.destroy', $alumno->id_alumno) }}" method="POST" id="deleteAlumno{{ $alumno->id_alumno }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="confirmarEliminarAlumno{{ $alumno->id_alumno }}(event)">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>

                                    <script>
                                        function confirmarEliminarAlumno{{ $alumno->id_alumno }}(event) {
                                            event.preventDefault();

                                            Swal.fire({
                                                title: 'Desea eliminar este alumno?',
                                                icon: 'question',
                                                showDenyButton: true,
                                                confirmButtonText: 'Eliminar',
                                                confirmButtonColor: '#a5161d',
                                                denyButtonText: 'Cancelar',
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    document.getElementById('deleteAlumno{{ $alumno->id_alumno }}').submit();
                                                }
                                            });
                                        }
                                    </script>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@if (Session::has('mensaje'))
    <script>
        Swal.fire({
            icon: "{{ Session::get('icono') }}",
            title: "{{ Session::get('mensaje') }}",
            showConfirmButton: false,
            timer: 4000
        });
    </script>
@endif
