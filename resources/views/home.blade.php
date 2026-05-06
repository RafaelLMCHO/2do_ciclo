@extends('adminlte::page')

@section('title', 'Panel de Control')

@section('content_header')
    <h1>Panel principal</h1>
@stop

@section('content')
    @if (auth()->user()->id_rol == 4)
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Perfil de apoderado</h3>
            </div>
            <div class="card-body">
                <p>Desde aqui puedes consultar las notas registradas de tus hijos/as.</p>
                <a href="{{ route('apoderado.consulta') }}" class="btn btn-info">
                    <i class="fas fa-file-alt mr-1"></i> Consulta
                </a>
            </div>
        </div>
    @elseif (auth()->user()->id_rol == 2)
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Perfil de profesor</h3>
            </div>
            <div class="card-body">
                <p>Tu usuario puede iniciar y cerrar sesion correctamente.</p>
                <p class="mb-0">Por ahora no tienes modulos habilitados por el administrador. Cuando te autoricen, aqui aparecera tu acceso al horario.</p>
            </div>
        </div>
    @else
        @if (auth()->user()->id_rol == 1)
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Consulta academica</h3>
                </div>
                <div class="card-body">
                    <p>Desde aqui puedes consultar las notas de todos los alumnos registrados.</p>
                    <a href="{{ route('apoderado.consulta') }}" class="btn btn-info">
                        <i class="fas fa-file-alt mr-1"></i> Consultar notas
                    </a>
                </div>
            </div>

            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Gestion de profesores</h3>
                </div>
                <div class="card-body">
                    <p>Desde aqui puedes habilitar o restringir el acceso de cada profesor a su modulo de horario.</p>
                    <a href="{{ route('admin.profesores.index') }}" class="btn btn-secondary">
                        <i class="fas fa-chalkboard-teacher mr-1"></i> Profesores
                    </a>
                </div>
            </div>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Gestion de alumnos</h3>
                </div>
                <div class="card-body">
                    <p>Desde aqui puedes crear, modificar y eliminar alumnos, incluyendo su usuario y contraseña.</p>
                    <a href="{{ route('admin.alumnos.index') }}" class="btn btn-primary">
                        <i class="fas fa-user-graduate mr-1"></i> Alumnos
                    </a>
                </div>
            </div>

            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Gestion de cursos</h3>
                </div>
                <div class="card-body">
                    <p>Desde aqui puedes crear, modificar, eliminar y buscar cursos disponibles en la institucion.</p>
                    <a href="{{ route('admin.cursos.index') }}" class="btn btn-success">
                        <i class="fas fa-book mr-1"></i> Cursos
                    </a>
                </div>
            </div>
        @endif

        <p>Bienvenido a la seccion de configuracion del sistema.</p>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Configuracion del sistema</h3>
            </div>
            <div class="card-body">
                <p>En esta seccion puedes configurar las opciones del sistema.</p>
                @if ($configuracion)
                    <ul>
                        <li><strong>Nombre del sistema:</strong> {{ $configuracion->nombre }}</li>
                        <li><strong>Descripcion:</strong> {{ $configuracion->descripcion }}</li>
                        <li><strong>Version:</strong> {{ $configuracion->version ?? '1.0' }}</li>
                        <li><strong>Fecha de creacion:</strong> {{ $configuracion->created_at }}</li>
                    </ul>
                @else
                    <p>Aun no has configurado los datos del sistema.</p>
                    <a href="{{ url('/admin/configuracion') }}" class="btn btn-primary">Ir a Configuracion</a>
                @endif
            </div>
        </div>
    @endif
@stop

@section('css')
@stop

@section('js')
    <script>
        console.log('AdminLTE cargado correctamente');
    </script>
@stop
