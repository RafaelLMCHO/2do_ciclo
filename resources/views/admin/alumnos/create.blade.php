@extends('adminlte::page')

@section('title', 'Crear Alumno')

@section('content_header')
    <h1><b>Creacion de un Nuevo Alumno</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Complete los datos del alumno</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.alumnos.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>CI</label><b> (*)</b>
                            <input type="text" class="form-control" name="ci" value="{{ old('ci') }}" required>
                            @error('ci')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombres</label><b> (*)</b>
                            <input type="text" class="form-control" name="nombres" value="{{ old('nombres') }}" required>
                            @error('nombres')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Apellido Paterno</label><b> (*)</b>
                            <input type="text" class="form-control" name="ap_paterno" value="{{ old('ap_paterno') }}" required>
                            @error('ap_paterno')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Apellido Materno</label><b> (*)</b>
                            <input type="text" class="form-control" name="ap_materno" value="{{ old('ap_materno') }}" required>
                            @error('ap_materno')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Genero</label><b> (*)</b>
                            <select name="genero" class="form-control" required>
                                <option value="">Seleccione</option>
                                <option value="F" {{ old('genero') === 'F' ? 'selected' : '' }}>Femenino</option>
                                <option value="M" {{ old('genero') === 'M' ? 'selected' : '' }}>Masculino</option>
                            </select>
                            @error('genero')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Nacimiento</label><b> (*)</b>
                            <input type="date" class="form-control" name="fecha_nac" value="{{ old('fecha_nac') }}" required>
                            @error('fecha_nac')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Datos de acceso</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Usuario</label><b> (*)</b>
                            <input type="text" class="form-control" name="username" value="{{ old('username') }}" required>
                            @error('username')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Contraseña</label><b> (*)</b>
                            <input type="password" class="form-control" name="password" required>
                            @error('password')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirmar Contraseña</label><b> (*)</b>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>
                </div>

                <hr>
                <a href="{{ route('admin.alumnos.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </form>
        </div>
    </div>
@stop
