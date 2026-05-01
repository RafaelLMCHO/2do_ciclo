@extends('adminlte::page')

@section('title', 'Consulta de Notas')

@section('content_header')
    <h1>{{ $esAdmin ? 'Consulta general de notas' : 'Consulta de notas' }}</h1>
@stop

@section('content')
    @if (! $esAdmin && ! $apoderado)
        <div class="alert alert-warning">
            No se encontro un registro de apoderado vinculado al usuario autenticado.
        </div>
    @else
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">{{ $esAdmin ? 'Vista de administrador' : 'Perfil de apoderado' }}</h3>
            </div>
            <div class="card-body">
                @if ($esAdmin)
                    <p class="mb-2">Como administrador puedes consultar las notas de todos los alumnos registrados.</p>
                    <p class="mb-0">Tambien puedes filtrar por apoderado o por alumno para revisar casos puntuales.</p>
                @else
                    <p class="mb-2">
                        <strong>Apoderado:</strong>
                        {{ trim($apoderado->nombres . ' ' . $apoderado->ap_paterno . ' ' . $apoderado->ap_materno) }}
                    </p>
                    <p class="mb-0">Solo se muestran las notas de los alumnos vinculados a este apoderado en la tabla parentesco.</p>
                @endif
            </div>
        </div>

        @if ($esAdmin || $hijos->count() > 1)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $esAdmin ? 'Filtros de consulta' : 'Filtrar por alumno' }}</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('apoderado.consulta') }}" class="row align-items-end">
                            <div class="col-md-8">
                            <label for="hijo">Alumno</label>
                            <select name="hijo" id="hijo" class="form-control">
                                <option value="">Todos</option>
                                @foreach ($hijos as $hijo)
                                    <option value="{{ $hijo->id_alumno }}" {{ (string) $hijoSeleccionado === (string) $hijo->id_alumno ? 'selected' : '' }}>
                                        {{ $hijo->nombre_completo }}
                                        @if ($esAdmin)
                                            {{ $hijo->apoderados_detalle ? ' (' . $hijo->apoderados_detalle . ')' : '' }}
                                        @else
                                            ({{ $hijo->parentesco }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-3 mt-md-0">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search mr-1"></i> Consultar
                            </button>
                            <a href="{{ route('apoderado.consulta') }}" class="btn btn-secondary">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        @elseif (! $esAdmin && $hijos->count() === 1)
            <div class="alert alert-light border">
                Consultando automaticamente las notas de {{ $hijos->first()->nombre_completo }} porque este apoderado solo tiene un hijo vinculado.
            </div>
        @endif

        @forelse ($hijos as $hijo)
            @php
                $notas = $notasPorHijo->get($hijo->id_alumno, collect());
            @endphp

            @if (! $hijoSeleccionado || (int) $hijoSeleccionado === (int) $hijo->id_alumno)
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ $hijo->nombre_completo }}</h3>
                        <div class="card-tools">
                            @if ($esAdmin)
                                <span class="badge badge-secondary">
                                    {{ $hijo->apoderados ? $hijo->apoderados : 'Sin apoderado vinculado' }}
                                </span>
                            @else
                                <span class="badge badge-info">{{ $hijo->parentesco }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if ($notas->isEmpty())
                            <div class="p-3">
                                <p class="mb-0">No hay notas registradas para este alumno.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Gestion</th>
                                            <th>Curso</th>
                                            <th>Trimestre</th>
                                            <th>Materia</th>
                                            <th>Ser</th>
                                            <th>Saber</th>
                                            <th>Hacer</th>
                                            <th>Autoevaluacion</th>
                                            <th>Promedio final</th>
                                            <th>Descripcion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($notas as $nota)
                                            <tr>
                                                <td>{{ $nota->gestion }}</td>
                                                <td>{{ $nota->curso }}</td>
                                                <td>{{ $nota->trimestre }}</td>
                                                <td>{{ $nota->materia }}</td>
                                                <td>{{ $nota->ser }}</td>
                                                <td>{{ $nota->saber }}</td>
                                                <td>{{ $nota->hacer }}</td>
                                                <td>{{ $nota->autoevaluacion }}</td>
                                                <td><strong>{{ number_format((float) $nota->promediofinal, 2) }}</strong></td>
                                                <td>{{ $nota->descripcion }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @empty
            <div class="alert alert-info">
                {{ $esAdmin ? 'No hay alumnos registrados para la consulta.' : 'No hay alumnos vinculados a este apoderado.' }}
            </div>
        @endforelse
    @endif
@stop
