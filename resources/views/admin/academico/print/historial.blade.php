<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Historial Academico</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; font-size: 12px; }
        h1, h2 { margin: 0 0 8px; }
        .meta { margin-bottom: 16px; color: #374151; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <h1>Historial Academico</h1>
    @if($alumnoSeleccionado)
        <div class="meta">
            Estudiante: <strong>{{ $alumnoSeleccionado->nombre_completo }}</strong><br>
            CI: {{ $alumnoSeleccionado->ci }}<br>
            Promedio general: {{ $promedioGeneral !== null ? number_format((float) $promedioGeneral, 2) : '-' }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Gestion</th>
                <th>Curso</th>
                <th>Materia</th>
                <th>Trimestre</th>
                <th>SER</th>
                <th>SABER</th>
                <th>HACER</th>
                <th>Autoev.</th>
                <th>Promedio</th>
            </tr>
        </thead>
        <tbody>
            @forelse($notas as $nota)
                <tr>
                    <td>{{ $nota->gestion }}</td>
                    <td>{{ $nota->curso }}</td>
                    <td>{{ $nota->materia }}</td>
                    <td>{{ $nota->trimestre }}</td>
                    <td class="right">{{ $nota->ser }}</td>
                    <td class="right">{{ $nota->saber }}</td>
                    <td class="right">{{ $nota->hacer }}</td>
                    <td class="right">{{ $nota->autoevaluacion }}</td>
                    <td class="right">{{ number_format((float) $nota->promediofinal, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="9">No existen registros academicos para este estudiante.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
