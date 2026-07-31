<?php
$turnoDate = date('Y-m-d', strtotime($turno['fecha_hora']));
$turnoHoraInicio = date('H:i', strtotime($turno['fecha_hora']));
$turnoHoraFin = !empty($turno['fecha_hora_fin']) ? date('H:i', strtotime($turno['fecha_hora_fin'])) : date('H:i', strtotime($turno['fecha_hora'] . ' + ' . (int)$turno['duracion_minutos'] . ' minutes'));
?>
<div class="barra-titulo reveal">
    <div>
        <p class="etiqueta">Panel de administración</p>
        <h1>Editar Turno</h1>
    </div>
    <div>
        <a class="boton-secundario" href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=turnos">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Volver
        </a>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alerta error reveal">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="margin-right:0.6rem; flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<section class="panel reveal reveal-delay-1" style="max-width:600px; margin: 0 auto;">
    <h2 style="font-family:var(--font-heading); font-size:1.25rem; margin-bottom:1.5rem;">Detalles del Turno</h2>
    
    <div style="margin-bottom: 2rem; padding: 1.5rem; background: var(--bg-body); border-radius: var(--radius-lg); border: 1px solid var(--border);">
        <p style="margin-bottom: 0.5rem;"><strong>Cliente:</strong> <?= htmlspecialchars($turno['cliente_nombre']) ?></p>
        <p style="margin-bottom: 0.5rem;"><strong>Servicio:</strong> <?= htmlspecialchars($turno['servicio_nombre']) ?></p>
        <p style="margin-bottom: 0;"><strong>Contacto:</strong> <?= htmlspecialchars($turno['telefono']) ?></p>
    </div>

    <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=guardarEdicionTurno">
        <?= Seguridad::campoToken() ?>
        <input type="hidden" name="id" value="<?= (int)$turno['id'] ?>">

        <div class="grupo-formulario">
            <label for="fecha">Fecha del turno</label>
            <input type="date" id="fecha" name="fecha" value="<?= $turnoDate ?>" required class="input-campo">
        </div>

        <div style="display:flex; gap: 1rem; margin-bottom: 1.5rem;">
            <div class="grupo-formulario" style="flex:1;">
                <label for="hora_inicio">Hora de Inicio</label>
                <input type="time" id="hora_inicio" name="hora_inicio" value="<?= $turnoHoraInicio ?>" required class="input-campo">
            </div>
            
            <div class="grupo-formulario" style="flex:1;">
                <label for="hora_fin">Hora de Fin</label>
                <input type="time" id="hora_fin" name="hora_fin" value="<?= $turnoHoraFin ?>" required class="input-campo">
                <small style="display:block; margin-top:0.5rem; color:var(--text-low); font-size:0.8rem;">
                    Ajustá manualmente si este servicio tomará más o menos tiempo.
                </small>
            </div>
        </div>

        <button type="submit" class="boton-primario" style="width: 100%;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Guardar Cambios
        </button>
    </form>
</section>
