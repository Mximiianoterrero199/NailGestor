<?php
// Calculate stats
$totalPendientes = 0;
$totalConfirmados = 0;
$totalCancelados = 0;
$totalCompletados = 0;

foreach ($turnos as $t) {
    switch ($t['estado']) {
        case 'pendiente':   $totalPendientes++;  break;
        case 'confirmado':  $totalConfirmados++; break;
        case 'cancelado':   $totalCancelados++;  break;
        case 'completado':  $totalCompletados++; break;
    }
}
?>

<div class="barra-titulo">
    <div>
        <p class="etiqueta">Panel de administración</p>
        <h1>Gestión de turnos</h1>
    </div>
    <a class="boton-secundario" href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=salir" id="btn-salir">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Cerrar sesión
    </a>
</div>

<!-- Stats -->
<div class="stats-row">
    <div class="stat-card stat-pendiente">
        <span class="stat-value"><?= $totalPendientes ?></span>
        <span class="stat-label">Pendientes</span>
    </div>
    <div class="stat-card stat-confirmado">
        <span class="stat-value"><?= $totalConfirmados ?></span>
        <span class="stat-label">Confirmados</span>
    </div>
    <div class="stat-card stat-completado">
        <span class="stat-value"><?= $totalCompletados ?></span>
        <span class="stat-label">Completados</span>
    </div>
    <div class="stat-card stat-cancelado">
        <span class="stat-value"><?= $totalCancelados ?></span>
        <span class="stat-label">Cancelados</span>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alerta exito">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<section class="panel">
    <?php if (empty($turnos)): ?>
        <div class="estado-vacio">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <p>No hay turnos registrados por el momento.</p>
        </div>
    <?php else: ?>
        <div class="tabla-contenedor">
            <table id="tabla-turnos">
                <thead>
                    <tr>
                        <th>Fecha y hora</th>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turnos as $turno): ?>
                        <tr>
                            <td>
                                <strong><?= date('d/m/Y', strtotime($turno['fecha_hora'])) ?></strong>
                                <small><?= date('H:i', strtotime($turno['fecha_hora'])) ?> hs</small>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($turno['cliente_nombre']) ?></strong>
                            </td>
                            <td>
                                <?= htmlspecialchars($turno['servicio_nombre']) ?>
                                <small>$<?= number_format((float) $turno['precio'], 0, ',', '.') ?> · <?= (int) $turno['duracion_minutos'] ?> min</small>
                            </td>
                            <td>
                                <?= htmlspecialchars($turno['telefono']) ?>
                                <?php if (!empty($turno['email'])): ?>
                                    <small><?= htmlspecialchars($turno['email']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="estado estado-<?= htmlspecialchars($turno['estado']) ?>"><?= htmlspecialchars($turno['estado']) ?></span>
                            </td>
                            <td>
                                <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=cambiarEstado" class="formulario-estado">
                                    <input type="hidden" name="id" value="<?= (int) $turno['id'] ?>">
                                    <select name="estado" id="select-estado-<?= (int) $turno['id'] ?>">
                                        <option value="pendiente" <?= $turno['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                        <option value="confirmado" <?= $turno['estado'] === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                        <option value="cancelado" <?= $turno['estado'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                        <option value="completado" <?= $turno['estado'] === 'completado' ? 'selected' : '' ?>>Completado</option>
                                    </select>
                                    <button type="submit">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                        Guardar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
