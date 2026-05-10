@extends('adminlte::page')

@section('title', 'Editar Modulo')

@section('content_header')
    <h1><b>Editar Modulo</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-success">
        <div class="card-header"><h3 class="card-title">Datos del modulo</h3></div>
        <div class="card-body">
            <form action="{{ route('admin.modulos.update', $modulo) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.modulos.form')
                <a href="{{ route('admin.modulos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
            </form>
        </div>
    </div>
@stop
