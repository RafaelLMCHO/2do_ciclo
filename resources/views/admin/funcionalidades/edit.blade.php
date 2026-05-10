@extends('adminlte::page')

@section('title', 'Editar Funcionalidad')

@section('content_header')
    <h1><b>Editar Funcionalidad</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-success">
        <div class="card-header"><h3 class="card-title">Datos de la funcionalidad</h3></div>
        <div class="card-body">
            <form action="{{ route('admin.funcionalidades.update', $funcionalidad) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.funcionalidades.form')
                <a href="{{ route('admin.funcionalidades.index') }}" class="btn btn-secondary">Cancelar</a>
                <button class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
            </form>
        </div>
    </div>
@stop
