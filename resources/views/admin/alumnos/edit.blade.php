@extends('adminlte::page')

@section('title', 'Editar Alumno')

@section('content_header')
    <h1><b>Edicion de Alumno</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Actualice los datos del alumno</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.alumnos.update', $alumno->id_alumno) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>CI</label><b> (*)</b>
                            <input type="text" class="form-control" name="ci" value="{{ old('ci', $alumno->ci) }}" required>
                            @error('ci')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombres</label><b> (*)</b>
                            <input type="text" class="form-control" name="nombres" value="{{ old('nombres', $alumno->nombres) }}" required>
                            @error('nombres')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Apellido Paterno</label><b> (*)</b>
                            <input type="text" class="form-control" name="ap_paterno" value="{{ old('ap_paterno', $alumno->ap_paterno) }}" required>
                            @error('ap_paterno')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Apellido Materno</label><b> (*)</b>
                            <input type="text" class="form-control" name="ap_materno" value="{{ old('ap_materno', $alumno->ap_materno) }}" required>
                            @error('ap_materno')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Genero</label><b> (*)</b>
                            <select name="genero" class="form-control" required>
                                <option value="F" {{ old('genero', $alumno->genero) === 'F' ? 'selected' : '' }}>Femenino</option>
                                <option value="M" {{ old('genero', $alumno->genero) === 'M' ? 'selected' : '' }}>Masculino</option>
                            </select>
                            @error('genero')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Nacimiento</label><b> (*)</b>
                            <input type="date" class="form-control" name="fecha_nac" value="{{ old('fecha_nac', $alumno->fecha_nac) }}" required>
                            @error('fecha_nac')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Datos personales de acceso</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Usuario</label><b> (*)</b>
                            <input type="text" class="form-control" name="username" value="{{ old('username', optional($alumno->usuario)->username) }}" required>
                            @error('username')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nueva Contraseña</label>
                            <input type="password" class="form-control" name="password">
                            <small class="text-muted">Deje este campo vacio si no desea cambiar la contraseña.</small>
                            @error('password')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>
                    </div>
                </div>

                <hr>
                <a href="{{ route('admin.alumnos.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Actualizar
                </button>
            </form>
        </div>
    </div>
@stop
