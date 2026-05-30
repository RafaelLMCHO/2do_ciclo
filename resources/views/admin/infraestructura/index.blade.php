@extends('adminlte::page')

@section('title', 'Infraestructura')

@section('content_header')
    <h1><b>Gestionar Infraestructura</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">CU20: Recursos f&iacute;sicos del colegio</h3>
            <a href="{{ route('admin.infraestructura.create') }}" class="btn btn-primary btn-sm float-right">
                <i class="fas fa-plus"></i> Nuevo Ambiente
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.infraestructura.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Buscar</label>
                            <input type="text" name="search" class="form-control" placeholder="Nombre, capacidad, ubicacion o tipo..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estado" class="form-control">
                                <option value="">Todos</option>
                                @foreach($estados as $opcion)
                                    <option value="{{ $opcion }}" {{ ($estado ?? '') === $opcion ? 'selected' : '' }}>{{ $opcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('admin.infraestructura.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list"></i> Mostrar todo
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th style="width: 70px;" class="text-center">ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th class="text-center">Capacidad</th>
                            <th>Ubicaci&oacute;n</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Horarios</th>
                            <th style="width: 180px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aulas as $aula)
                            <tr>
                                <td class="text-center">{{ $aula->id_aula }}</td>
                                <td>{{ $aula->nombre }}</td>
                                <td>{{ $aula->tipo }}</td>
                                <td class="text-center">{{ $aula->capacidad }}</td>
                                <td>{{ $aula->ubicacion }}</td>
                                <td class="text-center">
                                    @php
                                        $badge = match($aula->estado) {
                                            'Activo' => 'success',
                                            'Mantenimiento' => 'warning',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badge }}">{{ $aula->estado }}</span>
                                </td>
                                <td class="text-center">{{ $aula->horarios_asignados }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('admin.infraestructura.edit', $aula->id_aula) }}" class="btn btn-success btn-sm" title="Editar ambiente">
                                        <i class="fas fa-pencil-alt"></i> Editar
                                    </a>
                                    <form action="{{ route('admin.infraestructura.destroy', $aula->id_aula) }}" method="POST" id="form-delete-{{ $aula->id_aula }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" title="Eliminar ambiente" onclick="confirmarEliminar({{ $aula->id_aula }}, '{{ addslashes($aula->nombre) }}')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No se encontraron recursos de infraestructura.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function confirmarEliminar(id, nombre) {
        Swal.fire({
            title: 'Estas seguro?',
            html: `Se eliminara el ambiente <strong>${nombre}</strong>.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Si, eliminar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-delete-' + id).submit();
            }
        });
    }

    @if (Session::has('mensaje'))
        Swal.fire({
            icon: "{{ Session::get('icono') }}",
            title: "{{ Session::get('mensaje') }}",
            showConfirmButton: false,
            timer: 3500
        });
    @endif
</script>
@stop
