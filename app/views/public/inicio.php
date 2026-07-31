<?php
$seoTitulo    = 'Brisa Nails Studio | Manicura, Nail Art y Turnos Online en Mendoza';
$seoDesc      = 'Reservá tu turno en Brisa Nails. Manicura, capping gel y softgel en Mendoza. Turnos de 1h 30min, agendá fácil y rápido en línea.';
$seoCanonical = ($config['base_url'] ?? '') . '/';
?>
<section class="portada reveal">
    <div>
        <p class="etiqueta">Brisa Nails — Studio</p>
        <h1>Tu momento de<br>belleza y calma</h1>
        <p>Elegí el servicio que mejor se adapte a vos y reservá tu turno para vivir una experiencia de cuidado única.</p>
        
        <div style="margin-top: 50px;">
            <a href="<?= htmlspecialchars($config['base_url']) ?>/?accion=reservar_form" class="boton-primario">
                Agendar un turno
            </a>
        </div>
    </div>
</section>

<?php if (!empty($mensaje)): ?>
    <div class="alerta exito reveal">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alerta error reveal">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<section id="servicios" class="reveal reveal-delay-1">
    <div class="servicios">
        <?php foreach ($servicios as $idx => $servicio): ?>
            <article class="tarjeta">
                <div class="detalle-servicio" style="border-bottom: 1px solid var(--border); padding-bottom: 10px; margin-bottom: 15px;">
                    <span class="duracion"><?= (int) $servicio['duracion_minutos'] ?> MINUTOS</span>
                </div>
                <h3><?= htmlspecialchars($servicio['nombre']) ?></h3>
                <p><?= htmlspecialchars($servicio['descripcion'] ?? 'Cuidado profesional para tus manos.') ?></p>
                <div class="detalle-servicio">
                    <span class="precio">$<?= number_format((float) $servicio['precio'], 0, ',', '.') ?></span>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Adicionales Domicilio -->
    <div class="reveal reveal-delay-2" style="margin-top: 6rem; background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 3rem; text-align: center;">
        <p class="etiqueta" style="margin-bottom: 1.5rem;">Servicio a Domicilio</p>
        <h2 style="font-family: var(--font-heading); font-size: 2rem; color: var(--text-high); margin-bottom: 1.5rem; font-style: italic;">¿Prefieres atención en tu hogar?</h2>
        <p style="max-width: 600px; margin: 0 auto 2.5rem; color: var(--text-med);">Ofrecemos servicio a domicilio con un recargo adicional dependiendo de la zona para cubrir gastos de traslado:</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1.5rem; max-width: 800px; margin: 0 auto;">
            <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
                <strong style="display: block; color: var(--text-high); margin-bottom: 0.5rem;">Lavalle</strong>
                <span class="precio" style="font-size: 1.1rem;">+$4.500</span>
            </div>
            <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
                <strong style="display: block; color: var(--text-high); margin-bottom: 0.5rem;">Tres de Mayo</strong>
                <span class="precio" style="font-size: 1.1rem;">+$2.500</span>
            </div>
            <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
                <strong style="display: block; color: var(--text-high); margin-bottom: 0.5rem;">Andacollo</strong>
                <span class="precio" style="font-size: 1.1rem;">+$1.500</span>
            </div>
            <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
                <strong style="display: block; color: var(--text-high); margin-bottom: 0.5rem;">Sguazzini</strong>
                <span class="precio" style="font-size: 1.1rem;">+$5.800</span>
            </div>
        </div>
    </div>
</section>

<!-- Sección Mis Trabajos -->
<section id="trabajos" class="reveal reveal-delay-3" style="margin-top: 8rem;">
    <div style="text-align: center; margin-bottom: 4rem;">
        <p class="etiqueta">Galería</p>
        <h2 style="font-family: var(--font-heading); font-size: 2.75rem; color: var(--text-high); font-style: italic;">Mis Trabajos</h2>
        <p style="max-width: 600px; margin: 1rem auto 0; color: var(--text-low);">Una pequeña muestra de la pasión y detalle que pongo en cada set de uñas.</p>
    </div>

    <?php if (empty($trabajos)): ?>
        <div style="text-align: center; padding: 4rem; background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-md); max-width:600px; margin:0 auto;">
            <p style="color: var(--text-low); font-style: italic;">Próximamente estaremos subiendo fotos de nuestros trabajos.</p>
        </div>
    <?php else: ?>
        <!-- Banda de marquesina infinita -->
        <div class="marquee-wrapper" id="marquee-wrapper">
            <div class="marquee-track" id="marquee-track">
                <?php
                // Duplicamos el array hasta tener al menos 10 ítems para que el loop luzca fluido
                $items = $trabajos;
                while (count($items) < 10) {
                    $items = array_merge($items, $trabajos);
                }
                // Emitimos dos copias: la segunda es el duplicado invisible al inicio para el loop
                foreach ([1, 2] as $pass):
                ?>
                    <?php foreach ($items as $trabajo): ?>
                        <div class="marquee-item">
                            <img src="<?= htmlspecialchars($config['base_url']) ?>/public/uploads/trabajos/<?= htmlspecialchars($trabajo['imagen']) ?>" alt="<?= htmlspecialchars($trabajo['titulo'] ?? 'Trabajo de manicura y nail art por Brisa Nails') ?>" width="320" height="400" <?= ($pass === 1) ? '' : 'aria-hidden="true"' ?> loading="lazy" decoding="async">
                            <?php if ($trabajo['titulo']): ?>
                                <div class="trabajo-overlay">
                                    <span><?= htmlspecialchars($trabajo['titulo']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<style>
.marquee-wrapper {
    position: relative;
    overflow: hidden;
    width: 100%;
    cursor: grab;
    user-select: none;
}
.marquee-wrapper:active { cursor: grabbing; }

.marquee-track {
    display: flex;
    gap: 1.25rem;
    width: max-content;
    will-change: transform;
}

.marquee-item {
    position: relative;
    flex-shrink: 0;
    width: 260px;
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: transform var(--transition-spring), box-shadow var(--transition-med);
}

.marquee-item:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: var(--shadow-lg), 0 0 32px var(--accent-glow);
}

.marquee-item img {
    width: 100%;
    height: 320px;
    object-fit: cover;
    display: block;
    pointer-events: none;
}

</style>

<script>
(function() {
    const track   = document.getElementById('marquee-track');
    if (!track) return;

    const SPEED   = 0.6; // px por frame
    let pos       = 0;
    let paused    = false;
    let raf;

    // Pausa al hover
    const wrapper = document.getElementById('marquee-wrapper');
    wrapper.addEventListener('mouseenter', () => { paused = true; });
    wrapper.addEventListener('mouseleave', () => { paused = false; });

    // Arrastre con mouse / touch
    let isDragging = false, startX = 0, startPos = 0;

    wrapper.addEventListener('mousedown', e => {
        isDragging = true; paused = true;
        startX = e.clientX; startPos = pos;
        wrapper.style.cursor = 'grabbing';
    });
    window.addEventListener('mouseup', () => {
        if (isDragging) { isDragging = false; paused = false; wrapper.style.cursor = 'grab'; }
    });
    window.addEventListener('mousemove', e => {
        if (!isDragging) return;
        pos = startPos - (e.clientX - startX);
    });

    // Touch
    wrapper.addEventListener('touchstart', e => {
        isDragging = true; paused = true;
        startX = e.touches[0].clientX; startPos = pos;
    }, { passive: true });
    window.addEventListener('touchend', () => { isDragging = false; paused = false; });
    window.addEventListener('touchmove', e => {
        if (!isDragging) return;
        pos = startPos - (e.touches[0].clientX - startX);
    }, { passive: true });

    function tick() {
        if (!paused && !isDragging) pos += SPEED;

        // Calcular mitad del ancho real (sin duplicado) para el loop
        const halfWidth = track.scrollWidth / 2;
        if (pos >= halfWidth) pos -= halfWidth;
        if (pos < 0) pos += halfWidth;

        track.style.transform = `translateX(-${pos}px)`;
        raf = requestAnimationFrame(tick);
    }

    raf = requestAnimationFrame(tick);
})();
</script>

