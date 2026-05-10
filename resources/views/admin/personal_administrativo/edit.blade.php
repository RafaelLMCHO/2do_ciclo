@extends('adminlte::page')

@section('title', 'Editar Personal Administrativo')

@section('content_header')
    <h1><b>Editar Personal Administrativo</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-success">
        <div class="card-header"><h3 class="card-title">Datos personales y laborales</h3></div>
        <div class="card-body">
            <form action="{{ route('admin.personal-administrativo.update', $personalAdministrativo) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.personal_administrativo.form')
                <a href="{{ route('admin.personal-administrativo.index') }}" class="btn btn-secondary">Cancelar</a>
                <button class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
            </form>
        </div>
    </div>
@stop
