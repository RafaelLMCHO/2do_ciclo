@extends('adminlte::page')

@section('title', 'Profesores')

@section('content_header')
    <h1><b>Profesores</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Accesos de profesores</h3>
            <div class="d-flex align-items-center">
                <span class="badge {{ $totalProfesores >= 20 ? 'badge-danger' : 'badge-primary' }} mr-3">
                    {{ $totalProfesores }} / 20 profesores
                </span>
                @if ($totalProfesores < 20)
                    <a href="{{ route('admin.profesores.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus"></i> Añadir Profesor
                    </a>
                @else
                    <button class="btn btn-secondary btn-sm" disabled title="Límite de 20 profesores alcanzado">
                        <i class="fas fa-user-plus"></i> Añadir Profesor
                    </button>
                @endif
            </div>
        </div>
        <div class="card-body">

            @if ($totalProfesores >= 20)
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span><strong>Capacidad al tope:</strong> Se ha alcanzado el límite máximo de 20 profesores. Para añadir uno nuevo, primero elimine un profesor existente.</span>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Profesor</th>
                            <th>Correo</th>
                            <th>Usuario</th>
                            <th>Horario habilitado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($profesores as $profesor)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ trim($profesor->nombre . ' ' . $profesor->ap_paterno . ' ' . $profesor->ap_materno) }}</td>
                                <td>{{ $profesor->correo }}</td>
                                <td>{{ optional($profesor->usuario)->username ?? 'Sin usuario' }}</td>
                                <td>
                                    @if (optional($profesor->permiso)->puede_ver_horario)
                                        <span class="badge badge-success">Si</span>
                                    @else
                                        <span class="badge badge-danger">No</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    {{-- Editar datos del profesor --}}
                                    <a href="{{ route('admin.profesores.editInfo', $profesor->id_profesor) }}"
                                       class="btn btn-primary btn-sm"
                                       title="Editar datos del profesor">
                                        <i class="fas fa-user-edit"></i> Editar
                                    </a>

                                    {{-- Editar acceso --}}
                                    <a href="{{ route('admin.profesores.edit', $profesor->id_profesor) }}"
                                       class="btn btn-success btn-sm"
                                       title="Editar acceso del profesor">
                                        <i class="fas fa-pencil-alt"></i> Editar acceso
                                    </a>

                                    {{-- Eliminar --}}
                                    <form action="{{ route('admin.profesores.destroy', $profesor->id_profesor) }}"
                                          method="POST"
                                          id="form-delete-{{ $profesor->id_profesor }}"
                                          style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="btn btn-danger btn-sm"
                                                title="Eliminar profesor"
                                                onclick="confirmarEliminar({{ $profesor->id_profesor }}, '{{ addslashes(trim($profesor->nombre . ' ' . $profesor->ap_paterno . ' ' . $profesor->ap_materno)) }}')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
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
            title: '¿Estás seguro?',
            html: `Estás a punto de eliminar al profesor <strong>${nombre}</strong>.<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Sí, eliminar',
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
            timer: 4000
        });
    @endif
</script>
@stop
