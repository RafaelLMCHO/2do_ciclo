<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Rendimiento Academico</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; font-size: 12px; }
        h1 { margin: 0 0 8px; }
        .meta { margin-bottom: 16px; color: #374151; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <h1>Rendimiento Academico</h1>
    @if($alumnoSeleccionado)
        <div class="meta">
            Estudiante: <strong>{{ $alumnoSeleccionado->nombre_completo }}</strong><br>
            CI: {{ $alumnoSeleccionado->ci }}<br>
            Promedio general: {{ $promedioGeneral !== null ? number_format((float) $promedioGeneral, 2) : '-' }}<br>
            Asistencia: {{ $asistencia['porcentaje'] !== null ? number_format((float) $asistencia['porcentaje'], 2).'%' : 'Sin registros' }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Gestion</th>
                <th>Curso</th>
                <th>Materia</th>
                <th>Promedio</th>
                <th>Mejor</th>
                <th>Menor</th>
                <th>Situacion</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rendimientoMaterias as $materia)
                <tr>
                    <td>{{ $materia->gestion }}</td>
                    <td>{{ $materia->curso }}</td>
                    <td>{{ $materia->materia }}</td>
                    <td class="right">{{ number_format((float) $materia->promedio, 2) }}</td>
                    <td class="right">{{ number_format((float) $materia->mejor, 2) }}</td>
                    <td class="right">{{ number_format((float) $materia->menor, 2) }}</td>
                    <td>{{ (float) $materia->promedio >= 51 ? 'Regular' : 'Bajo rendimiento' }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No existen datos academicos disponibles para esta operacion.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
