<div class="barra-titulo">
    <div>
        <p class="etiqueta">Reservas</p>
        <h1>Agendá tu turno</h1>
    </div>
    <a class="boton-secundario" href="<?= htmlspecialchars($config['base_url']) ?>/">Volver a inicio</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alerta error">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<section class="grilla">
    <div class="panel">
        <h2>1. Servicio y Fecha</h2>
        <div class="formulario">
            <label>
                ¿Qué servicio buscás?
                <select id="select-servicio" required>
                    <option value="">Seleccioná un servicio</option>
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?= (int) $servicio['id'] ?>">
                            <?= htmlspecialchars($servicio['nombre']) ?> — $<?= number_format((float) $servicio['precio'], 0, ',', '.') ?> (<?= $servicio['duracion_minutos'] ?> min)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                ¿Qué día te queda cómodo?
                <input type="date" id="input-fecha" required>
            </label>

            <div id="contenedor-horarios" style="margin-top: 10px;">
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 5px;">Horarios disponibles:</p>
                <div id="mensaje-horarios" style="font-size: 14px; color: var(--text-secondary);">Seleccioná un servicio y una fecha para ver los horarios.</div>
                <div class="grilla-horarios" id="grilla-horarios"></div>
            </div>
        </div>
    </div>

    <div class="panel">
        <h2>2. Tus Datos</h2>
        <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?accion=reservar" class="formulario" id="form-reservar">
            <!-- Hidden inputs mapped from Step 1 -->
            <input type="hidden" name="servicio_id" id="hidden-servicio">
            <input type="hidden" name="fecha" id="hidden-fecha">
            <input type="hidden" name="hora" id="hidden-hora">

            <label>
                Nombre completo
                <input type="text" name="nombre" required maxlength="100" placeholder="Ej: María García">
            </label>

            <label>
                Teléfono de contacto
                <input type="tel" name="telefono" required maxlength="20" placeholder="Ej: 11 2345-6789">
            </label>

            <label>
                Email <small style="text-transform:none;font-weight:400;color:var(--text-muted)">(opcional, para notificaciones)</small>
                <input type="email" name="email" maxlength="100" placeholder="Ej: maria@email.com">
            </label>

            <button type="submit" id="btn-reservar" disabled style="opacity: 0.5; cursor: not-allowed; margin-top: 10px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Confirmar Reserva
            </button>
            <p id="aviso-submit" style="color: var(--warning); font-size: 13px; text-align: center; margin-top: -5px;">Tenés que elegir un horario primero.</p>
        </form>
    </div>
</section>

<style>
.grilla-horarios {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 10px;
    margin-top: 10px;
}
.btn-horario {
    background: var(--bg-input);
    border: 1px solid var(--border);
    color: var(--text-primary);
    padding: 12px 10px;
    border-radius: var(--radius-sm);
    text-align: center;
    cursor: pointer;
    user-select: none;
    transition: all 0.2s;
    font-size: 14px;
    font-weight: 600;
}
.btn-horario:hover {
    background: rgba(200, 140, 180, 0.05);
    border-color: rgba(200, 140, 180, 0.3);
}
.btn-horario.seleccionado {
    background: var(--accent-glow);
    border-color: var(--accent);
    color: var(--accent);
    box-shadow: 0 0 0 1px var(--accent);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputFecha = document.getElementById('input-fecha');
    const selectServicio = document.getElementById('select-servicio');
    const grilla = document.getElementById('grilla-horarios');
    const mensaje = document.getElementById('mensaje-horarios');
    
    // Hidden fields
    const hServicio = document.getElementById('hidden-servicio');
    const hFecha = document.getElementById('hidden-fecha');
    const hHora = document.getElementById('hidden-hora');
    
    const btnSubmit = document.getElementById('btn-reservar');
    const avisoSubmit = document.getElementById('aviso-submit');

    // Restringir fechas pasadas
    const hoy = new Date();
    // Ajustar por zona horaria local
    hoy.setMinutes(hoy.getMinutes() - hoy.getTimezoneOffset());
    inputFecha.setAttribute('min', hoy.toISOString().split('T')[0]);

    function checkSubmitStatus() {
        if (hServicio.value && hFecha.value && hHora.value) {
            btnSubmit.disabled = false;
            btnSubmit.style.opacity = '1';
            btnSubmit.style.cursor = 'pointer';
            avisoSubmit.style.display = 'none';
        } else {
            btnSubmit.disabled = true;
            btnSubmit.style.opacity = '0.5';
            btnSubmit.style.cursor = 'not-allowed';
            avisoSubmit.style.display = 'block';
        }
    }

    async function cargarHorarios() {
        const fecha = inputFecha.value;
        const servicioId = selectServicio.value;

        // Limpiar selección actual
        hHora.value = '';
        checkSubmitStatus();

        if (!fecha || !servicioId) {
            grilla.innerHTML = '';
            mensaje.textContent = 'Seleccioná un servicio y una fecha para ver los horarios.';
            mensaje.style.display = 'block';
            return;
        }

        hServicio.value = servicioId;
        hFecha.value = fecha;

        mensaje.textContent = 'Cargando disponibilidad...';
        mensaje.style.display = 'block';
        grilla.innerHTML = '';

        try {
            const url = `<?= $config['base_url'] ?>/?accion=apiHorarios&fecha=${fecha}&servicio_id=${servicioId}`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.error) {
                mensaje.textContent = 'Ocurrió un error al cargar. Intentá en un momento.';
                return;
            }

            if (data.disponibles.length === 0) {
                mensaje.textContent = 'No hay turnos disponibles para este servicio en la fecha elegida.';
                return;
            }

            mensaje.style.display = 'none';
            data.disponibles.forEach(hora => {
                const div = document.createElement('div');
                div.className = 'btn-horario';
                div.textContent = hora;
                
                div.addEventListener('click', () => {
                    // Deseleccionar a los hermanos
                    document.querySelectorAll('.btn-horario').forEach(el => el.classList.remove('seleccionado'));
                    div.classList.add('seleccionado');
                    hHora.value = hora;
                    checkSubmitStatus();
                });

                grilla.appendChild(div);
            });

        } catch (err) {
            console.error(err);
            mensaje.textContent = 'Ocurrió un error de conexión.';
        }
    }

    inputFecha.addEventListener('change', cargarHorarios);
    selectServicio.addEventListener('change', cargarHorarios);
});
</script>
