@extends('adminlte::page')

@section('title', 'Mi Horario')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-calendar-week mr-2 text-primary"></i>Mi Horario Semanal</h1>
        @if($profesor)
            <span class="badge badge-primary badge-pill px-3 py-2" style="font-size:1rem;">
                {{ $profesor->nombre }} {{ $profesor->ap_paterno }} {{ $profesor->ap_materno }}
            </span>
        @endif
    </div>
@stop

@section('content')
@if($user->id_rol == 1)
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm" style="border-radius:12px; border-left: 5px solid #4361ee;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-search-users mr-2 text-primary"></i>Ver horario de otro profesor</h5>
                    <p class="text-muted mb-0 small">Selecciona un profesor de la lista para supervisar sus clases.</p>
                </div>
                <form action="{{ route('profesor.horario') }}" method="GET" class="form-inline">
                    <select name="id_profesor" class="form-control select2 mr-2" style="min-width:300px;">
                        @foreach($profesores as $p)
                            <option value="{{ $p->id_profesor }}" {{ $idProfesor == $p->id_profesor ? 'selected' : '' }}>
                                {{ $p->nombre }} {{ $p->ap_paterno }} {{ $p->ap_materno }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-eye mr-2"></i>Ver Horario
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    @php
        $colores = [
            'Lunes'     => ['bg' => '#4361ee', 'light' => '#eef0fd'],
            'Martes'    => ['bg' => '#7209b7', 'light' => '#f5eaff'],
            'Miércoles' => ['bg' => '#f72585', 'light' => '#fff0f7'],
            'Jueves'    => ['bg' => '#fb8500', 'light' => '#fff7ee'],
            'Viernes'   => ['bg' => '#2dc653', 'light' => '#edfff2'],
        ];
    @endphp

    @foreach($dias as $dia)
    <div class="col-12 col-md-6 col-xl-4 mb-4">
        <div class="card shadow-sm h-100" style="border-top: 4px solid {{ $colores[$dia]['bg'] }}; border-radius:12px;">
            <div class="card-header d-flex align-items-center" style="background:{{ $colores[$dia]['bg'] }}; border-radius: 8px 8px 0 0;">
                <i class="fas fa-calendar-day mr-2 text-white"></i>
                <h5 class="mb-0 text-white font-weight-bold">{{ $dia }}</h5>
                <span class="ml-auto badge badge-light text-dark">
                    {{ $horariosPorDia[$dia]->count() }} {{ $horariosPorDia[$dia]->count() == 1 ? 'clase' : 'clases' }}
                </span>
            </div>
            <div class="card-body p-2">
                @if($horariosPorDia[$dia]->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-coffee fa-2x mb-2"></i>
                        <p class="mb-0">Sin clases este día</p>
                    </div>
                @else
                    @foreach($horariosPorDia[$dia] as $h)
                    <div class="mb-2 p-3 rounded" style="background:{{ $colores[$dia]['light'] }}; border-left: 4px solid {{ $colores[$dia]['bg'] }};">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <strong style="color:{{ $colores[$dia]['bg'] }}; font-size:0.95rem;">
                                <i class="fas fa-book-open mr-1"></i>{{ $h->materia }}
                            </strong>
                            <span class="badge" style="background:{{ $colores[$dia]['bg'] }}; color:white; font-size:0.75rem;">
                                {{ $h->paralelo }}
                            </span>
                        </div>
                        <div class="text-muted" style="font-size:0.85rem;">
                            <i class="fas fa-clock mr-1"></i>
                            <strong>{{ substr($h->hora_inicio, 0, 5) }} – {{ substr($h->hora_fin, 0, 5) }}</strong>
                        </div>
                        <div class="text-muted" style="font-size:0.82rem;">
                            <i class="fas fa-school mr-1"></i> {{ $h->curso }}
                            &nbsp;|&nbsp;
                            <i class="fas fa-door-open mr-1"></i> {{ $h->aula }}
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Resumen total --}}
<div class="row mt-2">
    <div class="col-12">
        <div class="card shadow-sm" style="border-radius:12px; border-top: 4px solid #343a40;">
            <div class="card-body">
                <h6 class="font-weight-bold text-secondary mb-3">
                    <i class="fas fa-chart-bar mr-2"></i>Resumen de la semana
                </h6>
                <div class="row text-center">
                    @php $totalClases = collect($horariosPorDia)->flatten()->count(); @endphp
                    @foreach($dias as $dia)
                    <div class="col">
                        <div class="py-2 px-3 rounded" style="background:{{ $colores[$dia]['light'] }};">
                            <div style="font-size:1.5rem; font-weight:bold; color:{{ $colores[$dia]['bg'] }};">
                                {{ $horariosPorDia[$dia]->count() }}
                            </div>
                            <div style="font-size:0.75rem; color:#666;">{{ $dia }}</div>
                        </div>
                    </div>
                    @endforeach
                    <div class="col">
                        <div class="py-2 px-3 rounded" style="background:#f0f0f0;">
                            <div style="font-size:1.5rem; font-weight:bold; color:#343a40;">{{ $totalClases }}</div>
                            <div style="font-size:0.75rem; color:#666;">Total</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card { transition: box-shadow 0.2s; }
    .card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.12) !important; }
</style>
@stop
