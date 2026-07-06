<div class="card">
    <div class="card-header" style="border-bottom: 1px solid #e2e8f0;">
        <form action="{{ route($routeName) }}" method="GET" class="row align-items-end" style="row-gap: 0.5rem;">
            <div class="col-md-3">
                <label class="small text-muted mb-1">Buscar estudiante</label>
                <input type="text" name="search" id="academico-search" class="form-control" placeholder="CI, nombre o apellido" value="{{ $search ?? '' }}" style="border-radius: 8px;">
            </div>
            <div class="col-md-3">
                <label class="small text-muted mb-1">Estudiante</label>
                <select name="id_alumno" id="academico-id-alumno" class="form-control" style="border-radius: 8px;">
                    <option value="">Seleccione</option>
                    @foreach($alumnos as $alumno)
                        <option value="{{ $alumno->id_alumno }}" {{ (string) $idAlumno === (string) $alumno->id_alumno ? 'selected' : '' }}>
                            {{ $alumno->nombre_completo }} - {{ $alumno->ci }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="small text-muted mb-1">Gestion</label>
                <select name="id_gestion" id="academico-id-gestion" class="form-control" style="border-radius: 8px;">
                    <option value="">Todas</option>
                    @foreach($gestiones as $gestion)
                        <option value="{{ $gestion->id_gestion }}" {{ (string) $idGestion === (string) $gestion->id_gestion ? 'selected' : '' }}>{{ $gestion->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="small text-muted mb-1">Curso</label>
                <select name="id_curso" id="academico-id-curso" class="form-control" style="border-radius: 8px;">
                    <option value="">Todos</option>
                    @foreach($cursos as $curso)
                        <option value="{{ $curso->id_curso }}" {{ (string) $idCurso === (string) $curso->id_curso ? 'selected' : '' }}>{{ $curso->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="small text-muted mb-1">Materia</label>
                <select name="id_materia" class="form-control" style="border-radius: 8px;">
                    <option value="">Todas</option>
                    @foreach($materias as $materia)
                        <option value="{{ $materia->id_materia }}" {{ (string) $idMateria === (string) $materia->id_materia ? 'selected' : '' }}>{{ $materia->nombre }}</option>
                    @endforeach
                </select>
            </div>
            @if($requiereTrimestre ?? false)
                <div class="col-md-2">
                    <label class="small text-muted mb-1">Trimestre</label>
                    <select name="id_trimestre" class="form-control" style="border-radius: 8px;">
                        <option value="">Todos</option>
                        @foreach($trimestres as $trimestre)
                            <option value="{{ $trimestre->id_trimestre }}" {{ (string) $idTrimestre === (string) $trimestre->id_trimestre ? 'selected' : '' }}>
                                Trimestre {{ $trimestre->id_trimestre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-2 d-flex" style="gap: 0.35rem;">
                <button type="submit" class="btn btn-sm btn-primary" title="Buscar">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route($routeName) }}" class="btn btn-sm btn-secondary" title="Limpiar">
                    <i class="fas fa-list"></i>
                </a>
            </div>
        </form>
    </div>
</div>

@if($actualizarEstudiantesPorCursoGestion ?? false)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const gestion = document.getElementById('academico-id-gestion');
            const curso = document.getElementById('academico-id-curso');
            const estudiante = document.getElementById('academico-id-alumno');
            const search = document.getElementById('academico-search');
            const url = @json(route($estudiantesPorCursoGestionRoute ?? 'admin.historial-academico.alumnos'));

            if (!gestion || !curso || !estudiante) {
                return;
            }

            const renderOption = function (alumno, selectedId) {
                const option = document.createElement('option');
                option.value = alumno.id_alumno;
                option.textContent = `${alumno.nombre_completo} - ${alumno.ci}`;
                option.selected = String(alumno.id_alumno) === String(selectedId);
                return option;
            };

            const recargarEstudiantes = function () {
                const selectedId = estudiante.value;
                const params = new URLSearchParams({
                    id_gestion: gestion.value,
                    id_curso: curso.value,
                    search: search ? search.value : ''
                });

                estudiante.disabled = true;
                estudiante.innerHTML = '<option value="">Cargando estudiantes...</option>';

                fetch(`${url}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(response => response.ok ? response.json() : Promise.reject(response))
                    .then(alumnos => {
                        estudiante.innerHTML = '<option value="">Seleccione</option>';

                        alumnos.forEach(alumno => {
                            estudiante.appendChild(renderOption(alumno, selectedId));
                        });

                        const existeSeleccion = alumnos.some(alumno => String(alumno.id_alumno) === String(selectedId));
                        if (!existeSeleccion) {
                            estudiante.value = '';
                        }

                        if (alumnos.length === 0) {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'Sin estudiantes para la gestion y curso';
                            estudiante.appendChild(option);
                        }
                    })
                    .catch(() => {
                        estudiante.innerHTML = '<option value="">No se pudieron cargar estudiantes</option>';
                    })
                    .finally(() => {
                        estudiante.disabled = false;
                    });
            };

            gestion.addEventListener('change', recargarEstudiantes);
            curso.addEventListener('change', recargarEstudiantes);
        });
    </script>
@endif
