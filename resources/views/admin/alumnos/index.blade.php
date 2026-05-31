@extends('adminlte::page')
{{-- Diagrama de secuencia: esta vista representa la respuesta final del caso "Listar alumnos". --}}
{{-- Antes de llegar aqui, la ruta admin.alumnos.index llamo a AlumnoController@index y consulto los modelos. --}}

@section('title', 'Alumnos')
{{-- Titulo de la pagina del listado. --}}

@section('content_header')
    {{-- Paso vista: encabezado que el usuario ve al entrar al listado. --}}
    <h1><b>Listado de Alumnos</b></h1>
    <hr>
@stop

@section('content')
    {{-- Paso vista: tarjeta que muestra la informacion enviada por el controlador en la variable $alumnos. --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Alumnos registrados</h3>
            <div class="card-tools">
                {{-- Paso vista -> ruta: abre el formulario create mediante admin.alumnos.create. --}}
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
                            <th>Telefono</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Paso controlador -> vista: $alumnos es la coleccion que AlumnoController@index envio a esta pantalla. --}}
                        @foreach ($alumnos as $alumno)
                            <tr>
                                {{-- $loop->iteration muestra el numero de fila dentro del foreach. --}}
                                <td class="text-center">{{ $loop->iteration }}</td>
                                {{-- Datos leidos desde app/Models/Alumno.php. --}}
                                <td>{{ $alumno->ci }}</td>
                                <td>{{ trim($alumno->nombres . ' ' . $alumno->ap_paterno . ' ' . $alumno->ap_materno) }}</td>
                                <td>{{ $alumno->genero === 'F' ? 'Femenino' : 'Masculino' }}</td>
                                <td>{{ $alumno->fecha_nac }}</td>
                                <td>{{ $alumno->telefono }}</td>
                                {{-- usuario es la relacion entre Alumno y User definida en el modelo Alumno. --}}
                                <td>{{ optional($alumno->usuario)->username ?? 'Sin usuario' }}</td>
                                <td>
                                    <div class="d-flex justify-content-center" style="gap: 8px;">
                                        {{-- Paso vista -> ruta: envia al formulario de edicion del alumno seleccionado. --}}
                                        <a href="{{ route('admin.alumnos.edit', $alumno->id_alumno) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-pencil-alt"></i> Editar
                                        </a>
                                        {{-- Paso vista -> ruta: prepara una peticion DELETE hacia AlumnoController@destroy. --}}
                                        <form action="{{ route('admin.alumnos.destroy', $alumno->id_alumno) }}" method="POST" id="deleteAlumno{{ $alumno->id_alumno }}">
                                            {{-- Token CSRF requerido para formularios en Laravel. --}}
                                            @csrf
                                            {{-- Laravel interpreta este POST como DELETE. --}}
                                            @method('DELETE')
                                            {{-- Antes de enviar DELETE, se llama a la confirmacion con SweetAlert. --}}
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="confirmarEliminarAlumno{{ $alumno->id_alumno }}(event)">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>

                                    <script>
                                        // Paso vista: detiene el envio directo y pide confirmacion al usuario.
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
                                                    // Paso vista -> ruta: si confirma, se envia el formulario DELETE al backend.
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

{{-- Paso controlador -> vista: muestra el mensaje que llega por Session despues de crear, actualizar o eliminar. --}}
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
