@extends('adminlte::page')

@section('title', 'Nuevo Curso')

@section('content_header')
    <h1><b>Nuevo Curso</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Datos del Curso</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.cursos.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nombre">Nombre del Curso</label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej: 1ro de Secundaria" required>
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
