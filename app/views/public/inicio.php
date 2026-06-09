<section class="portada">
    <div>
        <p class="etiqueta">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            Reservas online
        </p>
        <h1>Tu turno de<br>manicura ideal</h1>
        <p>Conocé nuestros servicios, elegí el que más te guste y reservá tu horario disponible desde la comodidad de tu casa.</p>
        
        <div style="margin-top: 35px;">
            <a href="<?= htmlspecialchars($config['base_url']) ?>/?accion=reservar_form" style="display: inline-flex; align-items: center; gap: 10px; background: var(--gradient-brand); color: #fff; padding: 16px 36px; border-radius: 999px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 20px rgba(200, 140, 180, 0.4); transition: transform 0.2s;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Agendar un turno
            </a>
        </div>
    </div>
</section>

<?php if (!empty($mensaje)): ?>
    <div class="alerta exito">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alerta error">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<section style="max-width: 900px; margin: 0 auto;">
    <div class="seccion-titulo" style="text-align: center; margin-bottom: 30px;">
        Nuestros Servicios
    </div>
    <div class="servicios">
        <?php foreach ($servicios as $servicio): ?>
            <article class="tarjeta">
                <h3><?= htmlspecialchars($servicio['nombre']) ?></h3>
                <p><?= htmlspecialchars($servicio['descripcion'] ?? 'Servicio de manicura profesional') ?></p>
                <div class="detalle-servicio">
                    <span class="precio">$<?= number_format((float) $servicio['precio'], 0, ',', '.') ?></span>
                    <span class="duracion">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <?= (int) $servicio['duracion_minutos'] ?> min
                    </span>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
