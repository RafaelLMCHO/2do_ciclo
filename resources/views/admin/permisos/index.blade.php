@extends('adminlte::page')

@section('title', 'Permisos por Rol')

@section('content_header')
    <h1><b>Permisos por Rol</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title">Asignar funcionalidades</h3>
            <form action="{{ route('admin.permisos.index') }}" method="GET" class="form-inline ml-auto">
                <select name="id_rol" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    @foreach ($roles as $item)
                        <option value="{{ $item->id_rol }}" {{ (int) $rol->id_rol === (int) $item->id_rol ? 'selected' : '' }}>
                            {{ $item->tipo }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-info btn-sm"><i class="fas fa-search"></i> Ver rol</button>
            </form>
        </div>

        <form action="{{ route('admin.permisos.update', $rol) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                @if ((int) $rol->id_rol === \App\Enums\Rol::ADMIN->value)
                    <div class="alert alert-info">
                        El Administrador tiene todos los permisos por defecto. No necesita asignaciones manuales.
                    </div>
                @endif

                <div class="row">
                    @foreach ($modulos as $modulo)
                        <div class="col-lg-6">
                            <div class="card card-outline card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">{{ $modulo->nombre }}</h3>
                                </div>
                                <div class="card-body">
                                    @forelse ($modulo->funcionalidades as $funcionalidad)
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   id="funcionalidad_{{ $funcionalidad->id_funcionalidad }}"
                                                   name="funcionalidades[]"
                                                   value="{{ $funcionalidad->id_funcionalidad }}"
                                                   {{ in_array($funcionalidad->id_funcionalidad, $permisosAsignados, true) ? 'checked' : '' }}
                                                   {{ (int) $rol->id_rol === \App\Enums\Rol::ADMIN->value ? 'disabled' : '' }}>
                                            <label class="custom-control-label" for="funcionalidad_{{ $funcionalidad->id_funcionalidad }}">
                                                <strong>{{ $funcionalidad->nombre }}</strong>
                                                <br>
                                                <span class="text-muted">{{ $funcionalidad->descripcion }}</span>
                                            </label>
                                        </div>
                                    @empty
                                        <span class="text-muted">Sin funcionalidades registradas.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary" {{ (int) $rol->id_rol === \App\Enums\Rol::ADMIN->value ? 'disabled' : '' }}>
                    <i class="fas fa-save"></i> Guardar permisos
                </button>
            </div>
        </form>
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
