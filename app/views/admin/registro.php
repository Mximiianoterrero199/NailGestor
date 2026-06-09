<section class="panel panel-login">
    <div class="login-icon">
        <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
    </div>
    <h1>Registro de Administrador</h1>
    <p>Completá los datos para crear una nueva cuenta.</p>

    <?php if (!empty($error)): ?>
        <div class="alerta error">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=registrar" class="formulario" id="form-registro">
        <label>
            Usuario
            <input type="text" name="usuario" required placeholder="Elegí un nombre de usuario" minlength="3" maxlength="60">
        </label>

        <label>
            Contraseña
            <input type="password" name="clave" required placeholder="Mínimo 8 caracteres" minlength="8">
        </label>

        <label>
            Confirmar Contraseña
            <input type="password" name="clave_confirmar" required placeholder="Repetí la contraseña" minlength="8">
        </label>

        <button type="submit" id="btn-registrar">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            Crear cuenta
        </button>

        <div style="text-align: center; margin-top: 10px;">
            <a href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=login" style="color: var(--accent); font-size: 14px; font-weight: 500;">Ya tengo cuenta. Ingresar.</a>
        </div>
    </form>
</section>
