<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Libreta Academica</title>
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
    <h1>Libreta Academica</h1>
    @if($alumnoSeleccionado)
        <div class="meta">
            Estudiante: <strong>{{ $alumnoSeleccionado->nombre_completo }}</strong><br>
            CI: {{ $alumnoSeleccionado->ci }}<br>
            Estado de pagos: {{ $pagos['habilitada'] ? 'Al dia' : 'Con pendientes' }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Gestion</th>
                <th>Curso</th>
                <th>Materia</th>
                <th>Trimestre</th>
                <th>Promedio</th>
                <th>Situacion</th>
            </tr>
        </thead>
        <tbody>
            @forelse($notas as $nota)
                <tr>
                    <td>{{ $nota->gestion }}</td>
                    <td>{{ $nota->curso }}</td>
                    <td>{{ $nota->materia }}</td>
                    <td>{{ $nota->trimestre }}</td>
                    <td class="right">{{ number_format((float) $nota->promediofinal, 2) }}</td>
                    <td>{{ (float) $nota->promediofinal >= 51 ? 'Aprobado' : 'Reprobado' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No existen notas registradas para generar la libreta.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
