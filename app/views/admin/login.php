<section class="panel panel-login">
    <div class="login-icon">
        <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
    </div>
    <h1>Panel de administración</h1>
    <p>Ingresá tus credenciales para gestionar los turnos.</p>

    <?php if (!empty($error)): ?>
        <div class="alerta error">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alerta exito">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=ingresar" class="formulario" id="form-login">
        <label>
            Usuario
            <input type="text" name="usuario" required placeholder="Ingresá tu usuario" id="input-usuario">
        </label>

        <label>
            Contraseña
            <input type="password" name="clave" required placeholder="Ingresá tu contraseña" id="input-clave">
        </label>

        <button type="submit" id="btn-ingresar">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            Ingresar
        </button>

        <div style="text-align: center; margin-top: 10px;">
            <a href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=registro" style="color: var(--accent); font-size: 14px; font-weight: 500;">No tengo cuenta. Registrarme.</a>
        </div>
    </form>
</section>
