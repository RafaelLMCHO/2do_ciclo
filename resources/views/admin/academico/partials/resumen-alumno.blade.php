<div class="stats-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-icon si-blue"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-number" style="font-size: 1.25rem;">{{ $alumnoSeleccionado->nombre_completo }}</div>
        <div class="stat-label">CI {{ $alumnoSeleccionado->ci }}</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon si-green"><i class="fas fa-chart-line"></i></div>
        <div class="stat-number">{{ $promedioGeneral !== null ? number_format((float) $promedioGeneral, 2) : '-' }}</div>
        <div class="stat-label">Promedio general</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon si-orange"><i class="fas fa-clipboard-list"></i></div>
        <div class="stat-number">{{ $notas->count() }}</div>
        <div class="stat-label">Registros academicos</div>
    </div>
</div>
