@extends('adminlte::page')

@section('title', 'Nuevo Personal Administrativo')

@section('content_header')
    <h1><b>Nuevo Personal Administrativo</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header"><h3 class="card-title">Datos personales y laborales</h3></div>
        <div class="card-body">
            <form action="{{ route('admin.personal-administrativo.store') }}" method="POST">
                @csrf
                @include('admin.personal_administrativo.form')
                <a href="{{ route('admin.personal-administrativo.index') }}" class="btn btn-secondary">Cancelar</a>
                <button class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            </form>
        </div>
    </div>
@stop
