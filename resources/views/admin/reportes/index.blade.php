@extends('adminlte::page')

@section('title', 'Generar Reportes')

@section('content_header')
    <h1 class="text-dark font-weight-bold"><i class="fas fa-chart-line mr-2"></i>Modulo de Reportes</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow border-0 card-outline card-success card-premium">
                <div class="card-header bg-gradient-success text-white py-3">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-file-invoice mr-2"></i>Seleccione un Reporte
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="report-selector">
                        @if($rol === \App\Enums\Rol::ADMIN->value)
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option active" data-report="admin_usuarios">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-users-cog mr-2 text-success"></i>Usuarios del Sistema</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Lista de todos los usuarios registrados y sus roles.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="admin_bitacora">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-history mr-2 text-success"></i>Accesos y Sesiones</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Registro detallado de acciones y sesiones de usuarios.</small>
                            </a>
                        @endif

                        @if($rol === \App\Enums\Rol::SECRETARIA->value || $rol === \App\Enums\Rol::ADMIN->value)
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option @if($rol !== \App\Enums\Rol::ADMIN->value) active @endif" data-report="admin_estudiantes_curso">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-user-graduate mr-2 text-success"></i>Estudiantes por Curso</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Listado de alumnos inscritos por curso.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="admin_matriculas_gestion">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-id-card mr-2 text-success"></i>Matriculas por Gestion</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Reporte de matriculas anuales.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="admin_pagos_estado">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-money-check-alt mr-2 text-success"></i>Pagos y Estado de Cuenta</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Detalle de mensualidades, matriculas y estados.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="admin_mora">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-exclamation-circle mr-2 text-success"></i>Reporte de Mora</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Listado de obligaciones pendientes vencidas.</small>
                            </a>
                        @endif

                        @if($rol === \App\Enums\Rol::PROFESOR->value)
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option active" data-report="docente_notas">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-file-signature mr-2 text-success"></i>Notas por Curso y Materia</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Calificaciones de tus alumnos asignados.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="docente_asistencia">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-calendar-check mr-2 text-success"></i>Asistencia por Curso y Materia</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Asistencia de estudiantes en tus cursos asignados.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="admin_estudiantes_curso">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-user-graduate mr-2 text-success"></i>Estudiantes por Curso</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Listado de alumnos de tus cursos asignados.</small>
                            </a>
                        @endif

                        @if($rol === \App\Enums\Rol::APODERADO->value)
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option active" data-report="tutor_calificaciones">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-award mr-2 text-success"></i>Calificaciones del Estudiante</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Boleta de calificaciones de tus hijos.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="tutor_asistencia">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-calendar-check mr-2 text-success"></i>Asistencia del Estudiante</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Registro de asistencia de tus hijos.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="tutor_estado_cuenta">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-receipt mr-2 text-success"></i>Estado de Cuenta</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Control de mensualidades y pagos de tus hijos.</small>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow border-0 card-outline card-success card-premium">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title text-success font-weight-bold mb-0">
                        <i class="fas fa-filter mr-2"></i>Filtros y Busqueda
                    </h3>
                </div>
                <div class="card-body">
                    <form id="filter-form">
                        @csrf
                        <input type="hidden" name="tipo_reporte" id="tipo_reporte_input">

                        <div class="row">
                            <div class="col-md-6 form-group filter-item" id="filter-gestion">
                                <label class="font-weight-bold"><i class="fas fa-calendar-alt mr-1 text-secondary"></i>Gestion Academica</label>
                                <select name="id_gestion" class="form-control">
                                    <option value="">Seleccione Gestion</option>
                                    @foreach($catalogos['gestiones'] as $g)
                                        <option value="{{ $g->id_gestion }}">{{ $g->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group filter-item" id="filter-curso">
                                <label class="font-weight-bold"><i class="fas fa-graduation-cap mr-1 text-secondary"></i>Curso</label>
                                <select name="id_curso" class="form-control">
                                    <option value="">Seleccione Curso</option>
                                    @foreach($catalogos['cursos'] as $c)
                                        <option value="{{ $c->id_curso }}">{{ $c->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group filter-item" id="filter-materia">
                                <label class="font-weight-bold"><i class="fas fa-book mr-1 text-secondary"></i>Materia</label>
                                <select name="id_materia" class="form-control">
                                    <option value="">Seleccione Materia</option>
                                    @foreach($catalogos['materias'] as $m)
                                        <option value="{{ $m->id_materia }}">{{ $m->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group filter-item" id="filter-alumno">
                                <label class="font-weight-bold"><i class="fas fa-user-graduate mr-1 text-secondary"></i>Hijo / Estudiante</label>
                                <select name="id_alumno" class="form-control">
                                    <option value="">Seleccione Estudiante</option>
                                    @foreach($catalogos['hijos'] as $h)
                                        <option value="{{ $h->id_alumno }}">{{ $h->nombre_completo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 form-group filter-item" id="filter-search">
                                <label class="font-weight-bold"><i class="fas fa-search mr-1 text-secondary"></i>Buscar en Bitacora</label>
                                <input type="text" name="search" class="form-control" placeholder="Usuario, accion o IP">
                            </div>

                            <div class="col-md-6 form-group filter-item" id="filter-fecha-inicio">
                                <label class="font-weight-bold"><i class="fas fa-calendar-plus mr-1 text-secondary"></i>Fecha de Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control">
                            </div>
                            <div class="col-md-6 form-group filter-item" id="filter-fecha-fin">
                                <label class="font-weight-bold"><i class="fas fa-calendar-minus mr-1 text-secondary"></i>Fecha de Fin</label>
                                <input type="date" name="fecha_fin" class="form-control">
                            </div>
                        </div>

                        <div class="mt-3 text-right">
                            <button type="submit" class="btn btn-gradient-success btn-lg px-5 shadow">
                                <i class="fas fa-sync-alt mr-2 spinner-icon"></i>Generar Reporte
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow border-0 card-premium" id="results-card" style="display: none;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-success font-weight-bold mb-0">
                        <i class="fas fa-table mr-2"></i>Resultados Generados
                    </h3>
                    <div class="card-tools d-flex flex-wrap">
                        <button type="button" class="btn btn-outline-danger btn-sm mr-2 mb-1 export-btn" data-format="print">
                            <i class="fas fa-print mr-1"></i>Imprimir
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm mr-2 mb-1 export-btn" data-format="pdf">
                            <i class="fas fa-file-pdf mr-1"></i>PDF
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm mr-2 mb-1 export-btn" data-format="excel">
                            <i class="fas fa-file-excel mr-1"></i>Excel
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm mb-1 export-btn" data-format="csv">
                            <i class="fas fa-file-csv mr-1"></i>CSV
                        </button>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive" style="max-height: 500px;">
                    <table class="table table-hover table-striped table-valign-middle mb-0" id="results-table">
                        <thead class="thead-dark text-uppercase small"></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-premium {
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .report-option {
            transition: all 0.25s ease;
            border-left: 4px solid transparent;
        }
        .report-option.active {
            border-left-color: #28a745;
            background-color: #f4faf6 !important;
            color: #218838 !important;
        }
        .report-option:hover:not(.active) {
            background-color: #f8f9fa;
            border-left-color: #ddd;
            transform: translateX(4px);
        }
        .btn-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            border: none;
            color: white;
        }
        .btn-gradient-success:hover {
            background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
            color: white;
        }
        .icon-arrow {
            transition: transform 0.2s ease;
        }
        .report-option.active .icon-arrow {
            transform: rotate(90deg);
            color: #28a745 !important;
        }
        .thead-dark th {
            background-color: #343a40 !important;
            border-color: #454d55 !important;
            color: #fff !important;
            white-space: nowrap;
        }
        #results-table td {
            white-space: nowrap;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            const reportFilters = {
                admin_usuarios: [],
                admin_bitacora: ['search', 'fecha-inicio', 'fecha-fin'],
                admin_estudiantes_curso: ['gestion', 'curso'],
                admin_matriculas_gestion: ['gestion'],
                admin_pagos_estado: ['gestion', 'curso'],
                admin_mora: ['gestion', 'curso'],
                docente_notas: ['gestion', 'curso', 'materia'],
                docente_asistencia: ['gestion', 'curso', 'materia'],
                tutor_calificaciones: ['alumno'],
                tutor_asistencia: ['alumno'],
                tutor_estado_cuenta: ['alumno']
            };

            const labelsMap = {
                id_user: 'ID',
                id_bitacora: 'ID',
                id_alumno: 'ID Alumno',
                id_inscripcion: 'ID Inscripcion',
                username: 'Usuario',
                rol: 'Rol',
                fecha_hora: 'Fecha y Hora',
                accion: 'Accion',
                ip: 'IP',
                ci: 'CI',
                estudiante: 'Estudiante',
                genero: 'Genero',
                curso: 'Curso',
                paralelo: 'Paralelo',
                gestion: 'Gestion',
                fecha: 'Fecha',
                monto: 'Monto',
                estado: 'Estado',
                concepto: 'Concepto',
                descuento: 'Descuento',
                fecha_vencimiento: 'Vencimiento',
                estado_pago: 'Estado de Pago',
                materia: 'Materia',
                trimestre: 'Trimestre',
                ser: 'Ser',
                saber: 'Saber',
                hacer: 'Hacer',
                autoevaluacion: 'Autoevaluacion',
                promediofinal: 'Promedio Final'
            };

            function updateFilters(reportType) {
                $('.filter-item').hide();
                $('.filter-item :input').prop('required', false);

                const activeFilters = reportFilters[reportType] || [];
                activeFilters.forEach(filter => {
                    $(`#filter-${filter}`).show();
                    if (filter === 'alumno' || reportType === 'docente_notas' || reportType === 'docente_asistencia') {
                        $(`#filter-${filter} :input`).prop('required', true);
                    }
                });

                $('#tipo_reporte_input').val(reportType);
                $('#results-card').hide();
            }

            $('.report-option').click(function(e) {
                e.preventDefault();
                $('.report-option').removeClass('active');
                $(this).addClass('active');
                updateFilters($(this).data('report'));
            });

            const initialReport = $('.report-option.active').data('report');
            if (initialReport) {
                updateFilters(initialReport);
            }

            $('#filter-form').submit(function(e) {
                e.preventDefault();

                const reportType = $('#tipo_reporte_input').val();
                const spinner = $('.spinner-icon');
                spinner.addClass('fa-spin');

                $.ajax({
                    url: "{{ route('admin.reportes.generar') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        spinner.removeClass('fa-spin');

                        if (!response.success) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Reporte Vacio',
                                text: response.mensaje,
                                confirmButtonColor: '#28a745'
                            });
                            $('#results-card').hide();
                            return;
                        }

                        renderTable(response.datos);
                    },
                    error: function(xhr) {
                        spinner.removeClass('fa-spin');
                        const errors = xhr.responseJSON && xhr.responseJSON.errors;
                        const firstError = errors ? Object.values(errors)[0][0] : null;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: firstError || 'No se pudo generar el reporte. Intentelo de nuevo.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            function renderTable(data) {
                const thead = $('#results-table thead');
                const tbody = $('#results-table tbody');

                thead.empty();
                tbody.empty();

                if (!data.length) {
                    $('#results-card').hide();
                    return;
                }

                const fields = Object.keys(data[0]);
                const trHead = $('<tr>');
                fields.forEach(field => {
                    trHead.append($('<th>').text(labelsMap[field] || field.replaceAll('_', ' ').toUpperCase()));
                });
                thead.append(trHead);

                data.forEach(item => {
                    const trRow = $('<tr>');
                    fields.forEach(field => {
                        const val = item[field];
                        trRow.append($('<td>').text(val === null || val === undefined ? '-' : val));
                    });
                    tbody.append(trRow);
                });

                $('#results-card').fadeIn();
            }

            $('.export-btn').click(function() {
                const formato = $(this).data('format');
                const filters = $('#filter-form').serialize();
                const url = `{{ route('admin.reportes.exportar') }}?formato=${formato}&${filters}`;

                if (formato === 'print' || formato === 'pdf') {
                    window.open(url, '_blank');
                    return;
                }

                window.location.href = url;
            });
        });
    </script>
@stop
