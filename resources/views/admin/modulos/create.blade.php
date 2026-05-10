@extends('adminlte::page')

@section('title', 'Nuevo Modulo')

@section('content_header')
    <h1><b>Nuevo Modulo</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header"><h3 class="card-title">Datos del modulo</h3></div>
        <div class="card-body">
            <form action="{{ route('admin.modulos.store') }}" method="POST">
                @csrf
                @include('admin.modulos.form')
                <a href="{{ route('admin.modulos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            </form>
        </div>
    </div>
@stop
