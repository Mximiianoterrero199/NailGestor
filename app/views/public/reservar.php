<?php
$seoTitulo    = 'Reservá tu Turno | Brisa Nails Studio Mendoza';
$seoDesc      = 'Agendá tu cita de manicura, capping gel o softgel en Brisa Nails. Turnos de 1h 30min. Elegí día y horario en línea.';
$seoCanonical = ($config['base_url'] ?? '') . '/?accion=reservar_form';
?>
<div class="barra-titulo reveal">
    <div>
        <p class="etiqueta">Reservas Online</p>
        <h1>Agendá tu cita</h1>
    </div>
    <a class="boton-secundario" href="<?= htmlspecialchars($config['base_url']) ?>/">Volver</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alerta error reveal">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<section class="grilla reveal reveal-delay-1">
    <div class="panel">
        <h2 style="font-family: var(--font-heading); font-size: 1.5rem; margin-bottom: 2rem;">Selección de Servicio</h2>
        <div class="formulario">
            <label>Elegí un tratamiento</label>
            <select id="select-servicio" required>
                <option value="">Seleccioná un servicio</option>
                <?php foreach ($servicios as $servicio): ?>
                    <option value="<?= (int) $servicio['id'] ?>">
                        <?= htmlspecialchars($servicio['nombre']) ?> — $<?= number_format((float) $servicio['precio'], 0, ',', '.') ?> (<?= $servicio['duracion_minutos'] ?> min)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Fecha sugerida</label>
            <input type="date" id="input-fecha" required>
            <p id="aviso-dia-cerrado" style="display: none; color: #dc2626; font-size: 0.8125rem; margin-top: -1.5rem; margin-bottom: 1rem; font-weight: 500;">
                Este día no hay atención. Por favor elegí otro día.
            </p>

            <div id="contenedor-horarios" style="margin-top: 1.5rem;">
                <label>Horarios disponibles</label>
                <div id="mensaje-horarios" style="font-size: 0.875rem; color: var(--text-low);">Seleccioná un servicio y una fecha.</div>
                <div class="grilla-horarios" id="grilla-horarios"></div>
            </div>
        </div>
    </div>

    <div class="panel">
        <h2 style="font-family: var(--font-heading); font-size: 1.5rem; margin-bottom: 2rem;">Información del Cliente</h2>
        <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?accion=reservar" class="formulario" id="form-reservar">
            <?= Seguridad::campoToken() ?>
            <input type="hidden" name="servicio_id" id="hidden-servicio">
            <input type="hidden" name="fecha" id="hidden-fecha">
            <input type="hidden" name="hora" id="hidden-hora">

            <label>Nombre y Apellido</label>
            <input type="text" name="nombre" required maxlength="100" placeholder="Ej: María García">

            <label>WhatsApp / Teléfono</label>
            <input type="tel" name="telefono" required maxlength="20" placeholder="Ej: 11 2345 6789">

            <label>Correo electrónico (opcional)</label>
            <input type="email" name="email" maxlength="100" placeholder="Ej: nombre@correo.com">

            <button type="submit" id="btn-reservar" disabled style="width: 100%; margin-top: 1rem;">
                Confirmar Cita
            </button>
            <p id="aviso-submit" style="color: #991b1b; font-size: 0.75rem; text-align: center; margin-top: 1rem;">Por favor, elegí un horario para continuar.</p>
        </form>
    </div>
</section>

<style>
.grilla-horarios {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(85px, 1fr));
    gap: 0.5rem;
    margin-top: 0.5rem;
}
.btn-horario {
    background: var(--bg-soft);
    border: 1px solid var(--border);
    color: var(--text-med);
    padding: 0.75rem 0.5rem;
    border-radius: var(--radius-sm);
    text-align: center;
    cursor: pointer;
    font-size: 0.8125rem;
    font-weight: 500;
    transition: all 0.2s;
}
.btn-horario:hover {
    border-color: var(--accent);
}
.btn-horario.seleccionado {
    background: var(--text-high);
    border-color: var(--text-high);
    color: var(--bg-white);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputFecha = document.getElementById('input-fecha');
    const selectServicio = document.getElementById('select-servicio');
    const grilla = document.getElementById('grilla-horarios');
    const mensaje = document.getElementById('mensaje-horarios');
    const hServicio = document.getElementById('hidden-servicio');
    const hFecha = document.getElementById('hidden-fecha');
    const hHora = document.getElementById('hidden-hora');
    const btnSubmit = document.getElementById('btn-reservar');
    const avisoSubmit = document.getElementById('aviso-submit');
    const avisoCerrado = document.getElementById('aviso-dia-cerrado');

    // Schedule data from admin config
    const diasActivos = <?= json_encode($diasActivos ?? new stdClass()) ?>;

    const hoy = new Date();
    hoy.setMinutes(hoy.getMinutes() - hoy.getTimezoneOffset());
    inputFecha.setAttribute('min', hoy.toISOString().split('T')[0]);

    function checkSubmitStatus() {
        if (hServicio.value && hFecha.value && hHora.value) {
            btnSubmit.disabled = false;
            avisoSubmit.style.display = 'none';
        } else {
            btnSubmit.disabled = true;
            avisoSubmit.style.display = 'block';
        }
    }

    function checkDiaCerrado() {
        const fecha = inputFecha.value;
        if (!fecha) {
            avisoCerrado.style.display = 'none';
            return false;
        }
        // JS getDay returns 0=Sunday same as PHP date('w')
        const diaSemana = new Date(fecha + 'T12:00:00').getDay();
        const config = diasActivos[diaSemana];
        
        if (!config || !config.activo) {
            avisoCerrado.style.display = 'block';
            return true; // closed
        }
        avisoCerrado.style.display = 'none';
        return false;
    }

    async function cargarHorarios() {
        const fecha = inputFecha.value;
        const servicioId = selectServicio.value;
        hHora.value = '';
        checkSubmitStatus();

        if (!fecha || !servicioId) {
            grilla.innerHTML = '';
            mensaje.textContent = 'Seleccioná un servicio y una fecha.';
            mensaje.style.display = 'block';
            return;
        }

        // Check client-side if day is closed
        if (checkDiaCerrado()) {
            grilla.innerHTML = '';
            mensaje.textContent = '';
            mensaje.style.display = 'none';
            return;
        }

        hServicio.value = servicioId;
        hFecha.value = fecha;
        mensaje.textContent = 'Buscando horarios...';
        mensaje.style.display = 'block';
        grilla.innerHTML = '';

        try {
            const url = `<?= $config['base_url'] ?>/?accion=apiHorarios&fecha=${fecha}&servicio_id=${servicioId}`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.cerrado) {
                mensaje.textContent = 'Este día el estudio está cerrado.';
                return;
            }

            if (data.error || data.disponibles.length === 0) {
                mensaje.textContent = 'No hay disponibilidad para esta fecha.';
                return;
            }

            mensaje.style.display = 'none';
            data.disponibles.forEach(hora => {
                const div = document.createElement('div');
                div.className = 'btn-horario';
                div.textContent = hora;
                div.addEventListener('click', () => {
                    document.querySelectorAll('.btn-horario').forEach(el => el.classList.remove('seleccionado'));
                    div.classList.add('seleccionado');
                    hHora.value = hora;
                    checkSubmitStatus();
                });
                grilla.appendChild(div);
            });
        } catch (err) {
            mensaje.textContent = 'Error de conexión.';
        }
    }

    inputFecha.addEventListener('change', () => {
        checkDiaCerrado();
        cargarHorarios();
    });
    selectServicio.addEventListener('change', cargarHorarios);
});
</script>
