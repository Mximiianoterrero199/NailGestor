<?php
// Separar turnos por estado
$totalPendientes  = 0;
$totalConfirmados = 0;
$totalCancelados  = 0;

$turnosPendientes  = [];
$turnosHistorial   = []; // confirmados + cancelados

foreach ($turnos as $t) {
    switch ($t['estado']) {
        case 'pendiente':
            $totalPendientes++;
            $turnosPendientes[] = $t;
            break;
        case 'confirmado':
            $totalConfirmados++;
            $turnosHistorial[] = $t;
            break;
        case 'cancelado':
            $totalCancelados++;
            $turnosHistorial[] = $t;
            break;
        default:
            // compatibilidad con datos viejos
            $turnosPendientes[] = $t;
            break;
    }
}
?>

<!-- BARRA TÍTULO -->
<div class="barra-titulo reveal">
    <div>
        <p class="etiqueta">Panel de administración</p>
        <h1>Gestión de Turnos</h1>
    </div>
    <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
        <a class="boton-secundario" href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=horarios">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Horarios
        </a>
        <a class="boton-secundario" href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=trabajos">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Trabajos
        </a>
        <a class="boton-secundario" href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=registro">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
            Nuevo Admin
        </a>
        <a class="boton-secundario" href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=salir">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Salir
        </a>
    </div>
</div>

<!-- STATS -->
<div class="stats-row reveal reveal-delay-1">
    <div class="stat-card">
        <span class="stat-value"><?= $totalPendientes ?></span>
        <span class="stat-label">Pendientes</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= $totalConfirmados ?></span>
        <span class="stat-label">Confirmados</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= $totalCancelados ?></span>
        <span class="stat-label">Cancelados</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= count($turnos) ?></span>
        <span class="stat-label">Total</span>
    </div>
</div>

<!-- ALERTAS -->
<?php if (!empty($mensaje)): ?>
    <?php if (!empty($_GET['wa_link'])): ?>
        <div class="alerta exito reveal" style="border-left-color: #25D366; background: linear-gradient(135deg,#e8fbed,#d1fae5); color: #065f46;">
            <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
                <svg width="28" height="28" fill="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                <div style="flex:1;">
                    <strong style="display:block; margin-bottom:0.25rem;"><?= htmlspecialchars($mensaje) ?></strong>
                    <span style="font-size:0.8125rem; opacity:0.85;">¿Querés notificar al cliente por WhatsApp?</span>
                </div>
                <a href="<?= htmlspecialchars($_GET['wa_link']) ?>" target="_blank" class="boton-primario" style="background:linear-gradient(135deg,#25D366,#128C7E); padding:0.55rem 1.2rem; font-size:0.75rem;">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                    Enviar WhatsApp
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="alerta exito reveal">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="margin-right:0.6rem; flex-shrink:0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alerta error reveal">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="margin-right:0.6rem; flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- ===== TURNOS ACTIVOS (pendiente) ===== -->
<div style="display:flex; align-items:center; gap:1rem; margin-top:2.5rem; margin-bottom:1.25rem;">
    <h2 style="font-family:var(--font-heading); font-size:1.5rem; font-style:italic; color:var(--text-high); margin:0;">Turnos Activos</h2>
    <span style="background:var(--grad-primary); color:#fff; font-size:0.7rem; font-weight:700; padding:0.25rem 0.75rem; border-radius:var(--radius-pill); letter-spacing:0.08em;"><?= $totalPendientes ?> pendientes</span>
</div>

<section class="panel reveal reveal-delay-2" style="padding:0; overflow:hidden;">
    <?php if (empty($turnosPendientes)): ?>
        <div class="estado-vacio" style="padding:3rem;">
            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:var(--text-low); margin-bottom:1rem;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <p style="color:var(--text-med); font-size:0.9375rem;">¡Sin turnos pendientes! Todo al día. 🎉</p>
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
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turnosPendientes as $turno): ?>
                        <tr>
                            <td data-label="Fecha y hora">
                                <strong style="color:var(--text-high); display:block;"><?= date('d/m/Y', strtotime($turno['fecha_hora'])) ?></strong>
                                <small style="color:var(--accent); font-weight:600;">
                                    <?= date('H:i', strtotime($turno['fecha_hora'])) ?> hs
                                    <?php if (!empty($turno['fecha_hora_fin'])): ?>
                                        - <?= date('H:i', strtotime($turno['fecha_hora_fin'])) ?> hs
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td data-label="Cliente">
                                <strong style="color:var(--text-high);"><?= htmlspecialchars($turno['cliente_nombre']) ?></strong>
                            </td>
                            <td data-label="Servicio">
                                <span style="color:var(--text-med);"><?= htmlspecialchars($turno['servicio_nombre']) ?></span>
                                <small style="display:block; color:var(--text-low);">$<?= number_format((float)$turno['precio'], 0, ',', '.') ?> · <?= (int)$turno['duracion_minutos'] ?> min</small>
                            </td>
                            <td data-label="Contacto">
                                <span style="color:var(--text-med);"><?= htmlspecialchars($turno['telefono']) ?></span>
                                <?php if (!empty($turno['email'])): ?>
                                    <small style="display:block; color:var(--text-low);"><?= htmlspecialchars($turno['email']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td data-label="Estado">
                                <span class="estado estado-pendiente">Pendiente</span>
                            </td>
                            <td data-label="Acciones">
                                <div style="display:flex; gap:0.5rem; justify-content:flex-end; flex-wrap:wrap;">
                                    <!-- Editar -->
                                    <a href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=editarTurno&id=<?= (int)$turno['id'] ?>" class="boton-secundario" style="padding:0.4rem 1rem; font-size:0.75rem;">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                        Editar
                                    </a>
                                    <!-- Confirmar -->
                                    <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=cambiarEstado" style="margin:0;">
                                        <?= Seguridad::campoToken() ?>
                                        <input type="hidden" name="id" value="<?= (int)$turno['id'] ?>">
                                        <input type="hidden" name="estado" value="confirmado">
                                        <button type="submit" class="boton-primario" style="padding:0.4rem 1rem; font-size:0.75rem; background:linear-gradient(135deg,#10b981,#059669); box-shadow:0 4px 16px rgba(16,185,129,0.3);">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                            Confirmar
                                        </button>
                                    </form>
                                    <!-- Cancelar -->
                                    <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=cambiarEstado" style="margin:0;" onsubmit="return confirm('¿Cancelar este turno?');">
                                        <?= Seguridad::campoToken() ?>
                                        <input type="hidden" name="id" value="<?= (int)$turno['id'] ?>">
                                        <input type="hidden" name="estado" value="cancelado">
                                        <button type="submit" class="boton-eliminar" style="padding:0.4rem 1rem; font-size:0.75rem;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            Cancelar
                                        </button>
                                    </form>
                                    <!-- Eliminar -->
                                    <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=eliminar" style="margin:0;" onsubmit="return confirm('¿Eliminar permanentemente?');">
                                        <?= Seguridad::campoToken() ?>
                                        <input type="hidden" name="id" value="<?= (int)$turno['id'] ?>">
                                        <button type="submit" title="Eliminar" style="background:none; border:1px solid var(--border); color:var(--text-low); width:32px; height:32px; border-radius:var(--radius-pill); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; padding:0; transition:all var(--transition-fast); box-shadow:none;" onmouseover="this.style.borderColor='#ef4444';this.style.color='#ef4444';" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-low)';">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<!-- ===== HISTORIAL (confirmados + cancelados) ===== -->
<div style="display:flex; align-items:center; gap:1rem; margin-top:3rem; margin-bottom:1.25rem;">
    <h2 style="font-family:var(--font-heading); font-size:1.5rem; font-style:italic; color:var(--text-high); margin:0;">Historial de Turnos</h2>
    <span style="background:rgba(100,116,139,0.12); color:var(--text-med); font-size:0.7rem; font-weight:700; padding:0.25rem 0.75rem; border-radius:var(--radius-pill); letter-spacing:0.08em; border:1px solid var(--border);"><?= count($turnosHistorial) ?> registros</span>
</div>

<section class="panel reveal reveal-delay-3" style="padding:0; overflow:hidden;">
    <?php if (empty($turnosHistorial)): ?>
        <div class="estado-vacio" style="padding:3rem;">
            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:var(--text-low); margin-bottom:1rem;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
            <p style="color:var(--text-med); font-size:0.9375rem;">El historial está vacío por ahora.</p>
        </div>
    <?php else: ?>
        <div class="tabla-contenedor">
            <table id="tabla-historial">
                <thead>
                    <tr>
                        <th>Fecha y hora</th>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turnosHistorial as $turno): ?>
                        <tr style="opacity:0.85;">
                            <td data-label="Fecha y hora">
                                <strong style="color:var(--text-high); display:block;"><?= date('d/m/Y', strtotime($turno['fecha_hora'])) ?></strong>
                                <small style="color:var(--text-low);">
                                    <?= date('H:i', strtotime($turno['fecha_hora'])) ?> hs
                                    <?php if (!empty($turno['fecha_hora_fin'])): ?>
                                        - <?= date('H:i', strtotime($turno['fecha_hora_fin'])) ?> hs
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td data-label="Cliente">
                                <strong style="color:var(--text-high);"><?= htmlspecialchars($turno['cliente_nombre']) ?></strong>
                            </td>
                            <td data-label="Servicio">
                                <span style="color:var(--text-med);"><?= htmlspecialchars($turno['servicio_nombre']) ?></span>
                                <small style="display:block; color:var(--text-low);">$<?= number_format((float)$turno['precio'], 0, ',', '.') ?></small>
                            </td>
                            <td data-label="Contacto">
                                <span style="color:var(--text-med);"><?= htmlspecialchars($turno['telefono']) ?></span>
                            </td>
                            <td data-label="Estado">
                                <span class="estado estado-<?= htmlspecialchars($turno['estado']) ?>"><?= ucfirst(htmlspecialchars($turno['estado'])) ?></span>
                            </td>
                            <td data-label="Acciones">
                                <div style="display:flex; gap:0.5rem; justify-content:flex-end; flex-wrap:wrap;">
                                    <!-- Editar -->
                                    <a href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=editarTurno&id=<?= (int)$turno['id'] ?>" class="boton-secundario" style="padding:0.4rem 1rem; font-size:0.75rem;">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </a>
                                    <!-- Restaurar a pendiente -->
                                    <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=cambiarEstado" style="margin:0;">
                                        <?= Seguridad::campoToken() ?>
                                        <input type="hidden" name="id" value="<?= (int)$turno['id'] ?>">
                                        <input type="hidden" name="estado" value="pendiente">
                                        <button type="submit" class="boton-secundario" style="padding:0.4rem 0.9rem; font-size:0.75rem;">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                                            Restaurar
                                        </button>
                                    </form>
                                    <!-- Eliminar -->
                                    <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=eliminar" style="margin:0;" onsubmit="return confirm('¿Eliminar este registro del historial?');">
                                        <?= Seguridad::campoToken() ?>
                                        <input type="hidden" name="id" value="<?= (int)$turno['id'] ?>">
                                        <button type="submit" title="Eliminar" style="background:none; border:1px solid var(--border); color:var(--text-low); width:32px; height:32px; border-radius:var(--radius-pill); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; padding:0; transition:all var(--transition-fast); box-shadow:none;" onmouseover="this.style.borderColor='#ef4444';this.style.color='#ef4444';" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-low)';">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
