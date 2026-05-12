@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content_header')
    <h1><b>CU08 - Gestionar Perfil</b></h1>
    <hr>
@stop

@section('content')
    @php
        $registro = $perfil['registro'];
        $nombreCompleto = $registro
            ? trim(($registro->nombre ?? $registro->nombres ?? '') . ' ' . ($registro->ap_paterno ?? '') . ' ' . ($registro->ap_materno ?? ''))
            : $usuario->username;
        $usernameBloqueado = ! $puedeEditarUsername;
    @endphp

    <div class="row">
        <div class="col-lg-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-circle mr-1"></i>
                        Datos actuales
                    </h3>
                </div>
                <div class="card-body">
                    <strong>Usuario</strong>
                    <p class="text-muted mb-3">{{ $usuario->username }}</p>

                    <strong>Rol</strong>
                    <p class="text-muted mb-3">{{ $usuario->rol_nombre }}</p>

                    <strong>Tipo de perfil</strong>
                    <p class="text-muted mb-3">{{ $perfil['titulo'] }}</p>

                    <strong>Nombre completo</strong>
                    <p class="text-muted mb-0">{{ $nombreCompleto ?: 'Sin registro personal vinculado' }}</p>
                </div>
            </div>

            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lock mr-1"></i>
                        Campos bloqueados por el sistema
                    </h3>
                </div>
                <div class="card-body">
                    @if ($registro)
                        <dl class="mb-0">
                            @if (isset($registro->ci))
                                <dt>CI</dt>
                                <dd>{{ $registro->ci }}</dd>
                            @endif
                            @if (isset($registro->fecha_nac))
                                <dt>Fecha de nacimiento</dt>
                                <dd>{{ $registro->fecha_nac }}</dd>
                            @endif
                            <dt>Rol</dt>
                            <dd>{{ $usuario->rol_nombre }}</dd>
                        </dl>
                    @else
                        <p class="mb-0 text-muted">Este usuario no tiene datos personales vinculados.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-edit mr-1"></i>
                        Editar datos permitidos
                    </h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre de usuario</label>
                                    <input type="text"
                                           name="username"
                                           class="form-control @error('username') is-invalid @enderror"
                                           value="{{ old('username', $usuario->username) }}"
                                           @if ($usernameBloqueado) disabled @endif>
                                    @if ($usernameBloqueado)
                                        <small class="form-text text-muted">Bloqueado porque este perfil se vincula por usuario tecnico.</small>
                                    @endif
                                    @error('username')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            @if ($perfil['tipo'] === 'profesor')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Correo electronico</label>
                                        <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror"
                                               value="{{ old('correo', $registro->correo ?? '') }}">
                                        @error('correo')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            @if (in_array($perfil['tipo'], ['profesor', 'secretaria', 'apoderado'], true))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Telefono</label>
                                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                               value="{{ old('telefono', $registro->telefono ?? '') }}">
                                        @error('telefono')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            @if (in_array($perfil['tipo'], ['profesor', 'secretaria'], true))
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Direccion</label>
                                        <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                                               value="{{ old('direccion', $registro->direccion ?? '') }}">
                                        @error('direccion')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            @if ($perfil['tipo'] === 'apoderado')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Ocupacion</label>
                                        <input type="text" name="ocupacion" class="form-control @error('ocupacion') is-invalid @enderror"
                                               value="{{ old('ocupacion', $registro->ocupacion ?? '') }}">
                                        @error('ocupacion')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if (! $registro && ! in_array($perfil['tipo'], ['usuario'], true))
                            <div class="alert alert-warning mb-0">
                                No se encontro el registro personal vinculado a este usuario.
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key mr-1"></i>
                        Cambiar contrasena
                    </h3>
                </div>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Contrasena actual</label>
                                    <input type="password" name="current_password"
                                           class="form-control @error('current_password') is-invalid @enderror" required>
                                    @error('current_password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nueva contrasena</label>
                                    <input type="password" name="new_password"
                                           class="form-control @error('new_password') is-invalid @enderror" required>
                                    @error('new_password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Confirmar contrasena</label>
                                    <input type="password" name="new_password_confirmation" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Minimo 8 caracteres, con letras, mayusculas, minusculas, numeros y simbolos.
                        </small>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key"></i> Actualizar contrasena
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
