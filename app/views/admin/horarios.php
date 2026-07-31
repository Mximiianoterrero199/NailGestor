<?php
$diasNombres = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$horariosMap = [];
foreach ($horarios as $h) {
    $horariosMap[$h['dia_semana']] = $h;
}
?>

<div class="barra-titulo reveal">
    <div>
        <p class="etiqueta">Configuración</p>
        <h1>Horarios de Atención</h1>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a class="boton-secundario" href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=turnos">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Volver a Turnos
        </a>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alerta exito reveal">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="margin-right: 8px; vertical-align: middle; flex-shrink: 0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span style="vertical-align: middle;"><?= htmlspecialchars($mensaje) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alerta error reveal"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<section class="panel reveal reveal-delay-1" style="padding: 2rem;">
    <div class="duracion-info-banner">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <div>
            <strong>Duración fija por turno: 1 hora 30 minutos</strong>
            <p>Los slots se generan automáticamente cada 90 min dentro del rango que configures. El preview te muestra exactamente los horarios que verán las clientas.</p>
        </div>
    </div>
    
    <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=guardarHorarios" class="formulario">
        <?= Seguridad::campoToken() ?>
        <div class="horarios-grid">
            <?php for ($dia = 0; $dia <= 6; $dia++): 
                $h = $horariosMap[$dia] ?? null;
                $activo = $h ? (bool) $h['activo'] : false;
                $inicio = $h ? substr($h['hora_inicio'], 0, 5) : '09:00';
                $fin    = $h ? substr($h['hora_fin'], 0, 5) : '19:00';
            ?>
            <div class="horario-dia <?= $activo ? 'activo' : 'inactivo' ?>" id="dia-<?= $dia ?>">
                <div class="horario-dia-header">
                    <label class="toggle-switch">
                        <input type="checkbox" name="activo_<?= $dia ?>" value="1" <?= $activo ? 'checked' : '' ?> onchange="toggleDia(<?= $dia ?>)">
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="horario-dia-nombre"><?= $diasNombres[$dia] ?></span>
                    <span class="horario-dia-badge"><?= $activo ? 'Abierto' : 'Cerrado' ?></span>
                </div>
                <div class="horario-dia-body">
                    <div class="horario-rango">
                        <div>
                            <label>Desde</label>
                            <input type="time" name="inicio_<?= $dia ?>" id="inicio-<?= $dia ?>" value="<?= htmlspecialchars($inicio) ?>" <?= !$activo ? 'disabled' : '' ?> onchange="actualizarPreview(<?= $dia ?>)">
                        </div>
                        <span class="horario-separator">—</span>
                        <div>
                            <label>Hasta</label>
                            <input type="time" name="fin_<?= $dia ?>" id="fin-<?= $dia ?>" value="<?= htmlspecialchars($fin) ?>" <?= !$activo ? 'disabled' : '' ?> onchange="actualizarPreview(<?= $dia ?>)">
                        </div>
                    </div>
                    <div class="turnos-preview" id="preview-<?= $dia ?>">
                        <!-- Preview generado por JS -->
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <div style="margin-top: 2.5rem; text-align: right;">
            <button type="submit" class="boton-primario">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Guardar Horarios
            </button>
        </div>
    </form>
</section>

<style>
.duracion-info-banner {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
    padding: 1rem;
    border-radius: var(--radius-md);
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}
.duracion-info-banner strong { display: block; margin-bottom: 0.25rem; }
.duracion-info-banner p { margin: 0; font-size: 0.875rem; opacity: 0.9; }

.turnos-preview {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px dashed #ccc;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.turno-tag {
    font-size: 0.75rem;
    background: #e5e7eb;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    color: #374151;
}

.horarios-grid {
    display: grid;
    gap: 1rem;
}

.horario-dia {
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: all 0.3s ease;
}

.horario-dia.activo {
    border-color: var(--accent);
    background: var(--accent-soft);
}

.horario-dia.inactivo {
    opacity: 0.6;
    background: var(--bg-muted);
}

.horario-dia-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
}

.horario-dia-nombre {
    font-weight: 700;
    font-size: 1rem;
    color: var(--text-high);
    flex: 1;
}

.horario-dia-badge {
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 0.25rem 0.75rem;
    border-radius: 100px;
    background: #ecfdf5;
    color: #059669;
}

.horario-dia.inactivo .horario-dia-badge {
    background: #fef2f2;
    color: #dc2626;
}

.horario-dia-body {
    padding: 0 1.5rem 1.25rem;
}

.horario-dia.inactivo .horario-dia-body {
    display: none;
}

.horario-rango {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.horario-rango > div {
    flex: 1;
}

.horario-rango label {
    margin-bottom: 0.5rem;
}

.horario-rango input[type="time"] {
    margin-bottom: 0;
    padding: 0.75rem;
    font-size: 0.9375rem;
}

.horario-separator {
    color: var(--text-low);
    font-size: 1.5rem;
    padding-top: 1.5rem;
}

/* Toggle switch */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
}
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background: #ccc;
    border-radius: 24px;
    transition: 0.3s;
}
.toggle-slider::before {
    content: '';
    position: absolute;
    height: 18px; width: 18px;
    left: 3px; bottom: 3px;
    background: white;
    border-radius: 50%;
    transition: 0.3s;
}
.toggle-switch input:checked + .toggle-slider {
    background: var(--accent);
}
.toggle-switch input:checked + .toggle-slider::before {
    transform: translateX(20px);
}
</style>

<script>
const DURACION_TURNO = 90; // minutos fijos por turno

function actualizarPreview(dia) {
    const preview = document.getElementById('preview-' + dia);
    if (!preview) return;

    const inputInicio = document.getElementById('inicio-' + dia);
    const inputFin = document.getElementById('fin-' + dia);

    if (!inputInicio || !inputFin || inputInicio.disabled) {
        preview.innerHTML = '';
        return;
    }

    const [hInicio, mInicio] = inputInicio.value.split(':').map(Number);
    const [hFin, mFin] = inputFin.value.split(':').map(Number);

    const minInicio = hInicio * 60 + mInicio;
    const minFin = hFin * 60 + mFin;

    if (isNaN(minInicio) || isNaN(minFin) || minFin <= minInicio) {
        preview.innerHTML = '<span style="font-size:0.8rem;color:#9ca3af;">⚠️ Rango inválido</span>';
        return;
    }

    const slots = [];
    let cursor = minInicio;

    while (cursor + DURACION_TURNO <= minFin) {
        const h = String(Math.floor(cursor / 60)).padStart(2, '0');
        const m = String(cursor % 60).padStart(2, '0');
        const hE = String(Math.floor((cursor + DURACION_TURNO) / 60)).padStart(2, '0');
        const mE = String((cursor + DURACION_TURNO) % 60).padStart(2, '0');
        slots.push(`${h}:${m} – ${hE}:${mE}`);
        cursor += DURACION_TURNO;
    }

    if (slots.length === 0) {
        preview.innerHTML = '<span style="font-size:0.8rem;color:#9ca3af;">Sin turnos disponibles en este rango</span>';
        return;
    }

    const header = `<span style="font-size:0.75rem;font-weight:700;color:var(--text-med);display:block;margin-bottom:0.5rem;">
        📅 ${slots.length} turno${slots.length !== 1 ? 's' : ''} disponible${slots.length !== 1 ? 's' : ''}:
    </span>`;
    
    const tags = slots.map(s => `<span class="turno-tag">🕐 ${s}</span>`).join('');
    preview.innerHTML = header + tags;
}

function toggleDia(dia) {
    const container = document.getElementById('dia-' + dia);
    const checkbox = container.querySelector('input[type="checkbox"]');
    const inputs = container.querySelectorAll('input[type="time"]');
    const badge = container.querySelector('.horario-dia-badge');

    if (checkbox.checked) {
        container.classList.remove('inactivo');
        container.classList.add('activo');
        badge.textContent = 'Abierto';
        inputs.forEach(i => i.disabled = false);
    } else {
        container.classList.remove('activo');
        container.classList.add('inactivo');
        badge.textContent = 'Cerrado';
        inputs.forEach(i => i.disabled = true);
    }

    actualizarPreview(dia);
}

// Inicializar todos los previews al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    for (let dia = 0; dia <= 6; dia++) {
        actualizarPreview(dia);
    }
});
</script>
