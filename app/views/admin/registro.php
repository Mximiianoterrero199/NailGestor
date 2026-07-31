<section class="reveal" style="min-height: 85vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem;">
    <div class="panel-auth">
        <div style="text-align: center; margin-bottom: 3rem;">
            <div class="auth-icon-container">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            </div>
            <h1 style="font-family: var(--font-heading); font-size: clamp(1.75rem, 5vw, 2.25rem); color: var(--text-high); margin-bottom: 0.5rem; font-style: italic;">Nuevo Admin</h1>
            <p style="color: var(--text-low); font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase;">Portal de Gestión BrisaNails</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alerta error" style="margin-bottom: 2rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=registrar" class="formulario" id="form-registro">
            <?= Seguridad::campoToken() ?>
            <div style="margin-bottom: 1.5rem;">
                <label>USUARIO</label>
                <input type="text" name="usuario" required placeholder="Elegí un nombre de usuario" minlength="3" maxlength="60" style="margin-bottom: 0;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label>CONTRASEÑA</label>
                <input type="password" name="clave" required placeholder="Mínimo 8 caracteres" minlength="8" style="margin-bottom: 0;">
            </div>

            <div style="margin-bottom: 2.5rem;">
                <label>CONFIRMAR CONTRASEÑA</label>
                <input type="password" name="clave_confirmar" required placeholder="Repetí la contraseña" minlength="8" style="margin-bottom: 0;">
            </div>

            <button type="submit" id="btn-registrar" style="width: 100%; justify-content: center;">
                CREAR CUENTA
            </button>

            <div style="text-align: center; margin-top: 2.5rem; border-top: 1px solid var(--border); padding-top: 2rem;">
                <a href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=turnos" style="color: var(--text-low); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;">&larr; Volver al Panel</a>
            </div>
        </form>
    </div>
</section>
