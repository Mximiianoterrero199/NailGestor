<section class="reveal" style="min-height: 85vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem;">
    <div class="panel-auth">
        <div style="text-align: center; margin-bottom: 3rem;">
            <div class="auth-icon-container">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <h1 style="font-family: var(--font-heading); font-size: clamp(1.75rem, 5vw, 2.25rem); color: var(--text-high); margin-bottom: 0.5rem; font-style: italic;">Acceso Admin</h1>
            <p style="color: var(--text-low); font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase;">Portal de Gestión BrisaNails</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alerta error" style="margin-bottom: 2rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['success'])): ?>
            <div class="alerta exito" style="margin-bottom: 2rem;">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=ingresar" class="formulario" id="form-login">
            <?= Seguridad::campoToken() ?>
            <div style="margin-bottom: 1.5rem;">
                <label>USUARIO</label>
                <input type="text" name="usuario" required placeholder="Ingresá tu usuario" id="input-usuario" style="margin-bottom: 0;">
            </div>

            <div style="margin-bottom: 2.5rem;">
                <label>CONTRASEÑA</label>
                <input type="password" name="clave" required placeholder="••••••••" id="input-clave" style="margin-bottom: 0;">
            </div>

            <button type="submit" id="btn-ingresar" style="width: 100%; justify-content: center;">
                INGRESAR AL PANEL
            </button>

        </form>
    </div>
</section>
