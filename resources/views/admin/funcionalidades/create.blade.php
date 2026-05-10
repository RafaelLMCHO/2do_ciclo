@extends('adminlte::page')

@section('title', 'Nueva Funcionalidad')

@section('content_header')
    <h1><b>Nueva Funcionalidad</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header"><h3 class="card-title">Datos de la funcionalidad</h3></div>
        <div class="card-body">
            <form action="{{ route('admin.funcionalidades.store') }}" method="POST">
                @csrf
                @include('admin.funcionalidades.form')
                <a href="{{ route('admin.funcionalidades.index') }}" class="btn btn-secondary">Cancelar</a>
                <button class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            </form>
        </div>
    </div>
@stop
