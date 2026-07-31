<div class="barra-titulo">
    <div>
        <p class="etiqueta">Contenido</p>
        <h1>Mis Trabajos</h1>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=turnos" class="boton-secundario">
            Volver a Turnos
        </a>
    </div>
</div>

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

<div class="grilla">
    <!-- Panel de subida -->
    <div class="panel reveal">
        <h3 style="margin-bottom: 2rem; font-family: var(--font-heading); font-style: italic;">Añadir Nuevo Trabajo</h3>
        
        <form action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=guardarTrabajo" method="POST" enctype="multipart/form-data" class="formulario">
            <!-- CSRF Token -->
            <?= Seguridad::campoToken() ?>
            
            <label for="imagen">Imagen (Android/iPhone fotos)</label>
            <input type="file" name="imagen" id="imagen" accept="image/*" required style="padding: 0.7rem; background: var(--bg-soft);">
            
            <label for="titulo">Título o Descripción breve (Opcional)</label>
            <input type="text" name="titulo" id="titulo" placeholder="Ej: Softgel con Nail Art floral" maxlength="100">
            
            <button type="submit" class="boton-primario" style="width: 100%; justify-content: center;">
                Subir Imagen
            </button>
        </form>
    </div>

    <!-- Panel de listado -->
    <div class="panel reveal reveal-delay-1">
        <h3 style="margin-bottom: 2rem; font-family: var(--font-heading); font-style: italic;">Trabajos Publicados</h3>
        
        <div class="galeria-admin" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1rem;">
            <?php if (empty($trabajos)): ?>
                <p style="grid-column: 1 / -1; text-align: center; color: var(--text-low); padding: 2rem;">No has subido ningún trabajo aún.</p>
            <?php else: ?>
                <?php foreach ($trabajos as $trabajo): ?>
                    <div style="position: relative; border-radius: var(--radius-sm); overflow: hidden; background: var(--bg-muted); aspect-ratio: 1/1; border: 1px solid var(--border);">
                        <img src="<?= htmlspecialchars($config['base_url']) ?>/public/uploads/trabajos/<?= htmlspecialchars($trabajo['imagen']) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; top: 5px; right: 5px; display: flex; gap: 5px;">
                            <!-- Botón Modificar Descripción -->
                            <button type="button" onclick="editarDescripcion(<?= $trabajo['id'] ?>, '<?= htmlspecialchars($trabajo['titulo'] ?? '', ENT_QUOTES) ?>')" style="background: rgba(59, 130, 246, 0.9); border: none; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; padding: 0; cursor: pointer;" title="Modificar Descripción">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </button>
                            
                            <!-- Botón Eliminar -->
                            <form action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=eliminarTrabajo" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este trabajo?')">
                                <?= Seguridad::campoToken() ?>
                                <input type="hidden" name="id" value="<?= $trabajo['id'] ?>">
                                <button type="submit" style="background: rgba(220, 38, 38, 0.9); border: none; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; padding: 0; cursor: pointer;" title="Eliminar Imagen">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Formulario Oculto para Editar Descripción -->
<form id="form-editar-titulo" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=actualizarTrabajo" method="POST" style="display: none;">
    <?= Seguridad::campoToken() ?>
    <input type="hidden" name="id" id="editar-id">
    <input type="hidden" name="titulo" id="editar-titulo">
</form>

<script>
function editarDescripcion(id, tituloActual) {
    const nuevoTitulo = prompt("Modificar descripción del trabajo (Deja en blanco si quieres quitarla):", tituloActual);
    
    // Si presiona Cancelar, nuevoTitulo es null. Si presiona OK con texto, es string.
    if (nuevoTitulo !== null) {
        document.getElementById('editar-id').value = id;
        document.getElementById('editar-titulo').value = nuevoTitulo;
        document.getElementById('form-editar-titulo').submit();
    }
}
</script>
