<?php
// Si $baseUrl no está definida, se adapta automáticamente
if (!isset($baseUrl)) {
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    $script = str_replace('\\', '/', $script);
    $script = $script === '/' ? '' : $script;
    $baseUrl = $protocolo . $host . $script;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $titulo ?? ' BrisaNails | Gestión de Turnos Profesionales' ?></title>
    <meta name="description" content="Gestiona tus turnos de manera eficiente y descubre nuestros servicios exclusivos de manicura en BrisaNails.">
    <meta name="author" content="BrisaNails">
    
    <meta property="og:title" content="BrisaNails | Uñas Profesionales">
    <meta property="og:description" content="Reserva tu turno de manera rápida y segura en BrisaNails.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://tudominio.com/">
    
    <meta name="theme-color" content="#FF0082">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400&display=swap"></noscript>

    <link rel="icon" type="image/jpeg" href="<?= htmlspecialchars($baseUrl) ?>/public/logo.jpg">

    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/css/estilos.css?v=<?= time() ?>">

    <!--
        Red de seguridad responsive para el header/nav.
        No tengo acceso a estilos.css (no fue subido), así que esto es un
        complemento liviano que solo actúa en pantallas chicas, sin pisar
        nada de tu hoja de estilos principal. Si ya tenés ahí un menú
        hamburguesa o reglas propias para mobile, podés borrar este bloque.
    -->
    <style>
        @media (max-width: 480px) {
            .encabezado {
                flex-wrap: wrap;
                row-gap: 0.5rem;
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .navegacion {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.5rem 0.9rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <header class="encabezado">
        <a class="marca" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/" aria-label="BrisaNails - Inicio">
            <span class="logo-header-texto">BrisaNails</span>
        </a>
        <nav class="navegacion" aria-label="Navegación principal">
            <?php if (($_GET['controlador'] ?? '') !== 'admin'): ?>
                <a class="nav-admin" href="<?= htmlspecialchars($baseUrl) ?>/#servicios">Ver servicios</a>
                <a class="nav-admin" href="#contacto">Contacto</a>
                <?php if (!empty($_SESSION['admin_autenticado'])): ?>
                    <a class="nav-admin" href="<?= htmlspecialchars($baseUrl) ?>/?controlador=admin&accion=turnos">Panel Admin</a>
                <?php else: ?>
                    <a class="nav-admin" href="<?= htmlspecialchars($baseUrl) ?>/?controlador=admin&accion=login">Admin</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
    </header>

    <main class="contenedor">
        <?php require $contenido; ?>
    </main>

    <style>
        .bn-footer {
            padding: 3rem 1.5rem;
            text-align: center;
        }
        .bn-footer-brand {
            margin-bottom: 2rem;
        }
        .bn-footer-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            gap: 0.5rem;
        }
        .bn-footer-text {
            text-align: left;
            flex: 1 1 auto;
            min-width: 0;
        }
        .bn-footer-text p {
            margin: 0;
            font-size: clamp(0.55rem, 2vw, 0.7rem);
            color: var(--text-low);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .bn-footer-social {
            display: flex;
            gap: 1.25rem;
            align-items: center;
            flex-shrink: 0;
        }
        .bn-social-link {
            display: inline-flex;
            transition: transform 0.25s ease;
            -webkit-tap-highlight-color: transparent;
        }
        .bn-social-link:hover,
        .bn-social-link:focus-visible {
            transform: scale(1.15);
        }
        .bn-social-link svg {
            width: 38px;
            height: 38px;
        }

        /* Mobile: menos relleno, íconos un poco más chicos, todo sigue en una fila */
        @media (max-width: 480px) {
            .bn-footer {
                padding: 2rem 1rem;
            }
            .bn-footer-brand {
                margin-bottom: 1.25rem;
                font-size: 0.95rem;
            }
            .bn-footer-row {
                gap: 0.75rem;
            }
            .bn-footer-social {
                gap: 0.85rem;
            }
            .bn-social-link svg {
                width: 30px;
                height: 30px;
            }
            .bn-footer-text p {
                font-size: 0.65rem;
            }
        }
    </style>

    <footer id="contacto" class="pie bn-footer">

        <div class="bn-footer-brand">
            <span>BrisaNails</span>
        </div>

        <div class="bn-footer-row">

            <div class="bn-footer-text">
                <p>
                    Gestión profesional de turnos &copy; <?= date('Y') ?>
                </p>
            </div>

            <div class="bn-footer-social">
                <a href="https://wa.me/5492612321836" target="_blank" rel="noopener noreferrer" aria-label="Contactar por WhatsApp" class="bn-social-link">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 175.216 175.552">
                        <defs><linearGradient id="wa-g" x1="85.915" x2="86.535" y1="32.567" y2="137.092" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#57d163"/><stop offset="1" stop-color="#23b33a"/></linearGradient></defs>
                        <path d="M87.184 25.227a62.185 62.185 0 0 0-44.188 18.225 61.859 61.859 0 0 0-18.256 44.129c-.029 10.376 2.798 20.547 8.181 29.41L26.384 149.17l32.644-8.527a62.327 62.327 0 0 0 28.124 6.845h.027a62.182 62.182 0 0 0 44.185-18.223 61.875 61.875 0 0 0 18.257-44.13 61.864 61.864 0 0 0-18.255-44.124 62.195 62.195 0 0 0-44.182-18.784z" fill="url(#wa-g)"/>
                        <path d="M68.772 55.603c-1.378-3.061-2.828-3.123-4.137-3.176l-3.524-.044c-1.226 0-3.218.46-4.902 2.3-1.678 1.843-6.407 6.254-6.407 15.232s6.556 17.661 7.475 18.882c.93 1.22 12.685 20.381( 31.507 27.75 15.567 6.122 18.725 4.908 22.097 4.604 3.372-.305 10.893-4.455 12.425-8.753 1.531-4.301 1.531-7.975 1.073-8.75-.459-.775-1.685-1.226-3.525-2.146-1.842-.919-10.896-5.371-12.578-5.983-1.684-.61-2.908-.919-4.137.921-1.225 1.842-4.748 5.978-5.819 7.207-1.071 1.225-2.144 1.381-3.984.462-1.842-.919-7.76-2.867-14.793-9.124-5.465-4.873-9.154-10.891-10.228-12.73-1.07-1.84-.114-2.835.806-3.752.824-.825 1.842-2.147 2.762-3.22.9-1.072 1.222-1.838 1.836-3.063.617-1.226.31-2.3-.154-3.22-.458-.918-4.275-10.375-5.972-14.196z" fill="#fff" fill-rule="evenodd"/>
                    </svg>
                </a>

                <a href="https://instagram.com/madafbrisa" target="_blank" rel="noopener noreferrer" aria-label="Ver perfil de Instagram" class="bn-social-link">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <defs><radialGradient id="ig-g" cx="158.429" cy="543.297" r="764.608" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#fdf497"/><stop offset=".1" stop-color="#fdf497"/><stop offset=".5" stop-color="#fd5949"/><stop offset=".68" stop-color="#d6249f"/><stop offset="1" stop-color="#285aeb"/></radialGradient></defs>
                        <path fill="url(#ig-g)" d="M256 0C186.96 0 178.09.29 150.73 1.61 123.43 2.93 104.8 7.35 88.5 13.78a131.06 131.06 0 0 0-47.38 30.85A131.18 131.18 0 0 0 13.28 91.9C6.82 108.17 2.4 126.77 1.09 154.04-.23 181.41-.54 190.27-.54 256c0 65.73.31 74.6 1.63 101.96 1.32 27.27 5.74 45.87 12.19 62.14a131.06 131.06 0 0 0 30.85 47.38 131.18 131.18 0 0 0 47.39 30.85c16.28 6.44 34.88 10.86 62.15 12.18C150.09 511.73 158.96 512 256 512s105.91-.27 133.27-1.59c27.27-1.32 45.87-5.74 62.14-12.18a136.67 136.67 0 0 0 78.23-78.23c6.44-16.27 10.86-34.87 12.18-62.14C513.14 330.6 513 321.73 513 256s.14-74.59-1.18-101.95c-1.32-27.27-5.74-45.87-12.18-62.15a131.18 131.18 0 0 0-30.85-47.38A131.06 131.06 0 0 0 421.41 13.8C405.14 7.35 386.54 2.93 359.27 1.61 331.91.29 323.04 0 256 0zm0 46.22c64.52 0 72.17.25 97.65 1.55 23.58 1.08 36.37 5.03 44.89 8.35 11.29 4.39 19.35 9.63 27.82 18.11 8.47 8.47 13.72 16.53 18.11 27.82 3.32 8.52 7.27 21.31 8.35 44.89 1.3 25.48 1.55 33.13 1.55 97.65s-.25 72.17-1.55 97.65c-1.08 23.58-5.03 36.37-8.35 44.89-4.39 11.29-9.64 19.35-18.11 27.82-8.47 8.47-16.53 13.72-27.82 18.11-8.52 3.32-21.31 7.27-44.89 8.35-25.48 1.3-33.12 1.55-97.65 1.55s-72.17-.25-97.65-1.55c-23.58-1.08-36.37-5.03-44.89-8.35-11.29-4.39-19.35-9.64-27.82-18.11-8.47-8.47-13.72-16.53-18.11-27.82-3.32-8.52-7.27-21.31-8.35-44.89-1.3-25.48-1.55-33.12-1.55-97.65s.25-72.17 1.55-97.65c1.08-23.58 5.03-36.37 8.35-44.89 4.39-11.29 9.64-19.35 18.11-27.82 8.47-8.47 16.53-13.72 27.82-18.11 8.52-3.32 21.31-7.27 44.89-8.35 25.48-1.3 33.13-1.55 97.65-1.55z"/>
                        <path fill="url(#ig-g)" d="M256 341.33a85.33 85.33 0 1 1 0-170.66 85.33 85.33 0 0 1 0 170.66zm0-216.89a131.56 131.56 0 1 0 0 263.12 131.56 131.56 0 0 0 0-263.12zm167.54-4.68a30.72 30.72 0 1 1-61.44 0 30.72 30.72 0 0 1 61.44 0z"/>
                    </svg>
                </a>
            </div>

        </div>

    </footer>

        <!-- Asistente de Inteligencia Artificial (n8n Chat) -->
        <link rel="preload" href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" as="style">
        <link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" media="print" onload="this.media='all'" />
        <noscript><link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" /></noscript>
        <style>
            /* =====================================================================
               Tema "BrisaNails Glam" para el widget de chat de n8n.
               Usa exclusivamente las variables CSS oficiales que expone @n8n/chat
               (ver dist/style.css) — no necesita hacks de Shadow DOM ni setTimeout,
               el widget NO usa Shadow DOM, así que las variables se heredan solas.
               ===================================================================== */
            :root {
                /* --- Paleta de color (fucsia de marca + violeta profundo) --- */
                --chat--color--primary: #FF0082;
                --chat--color--primary-shade-50: #E0006F;
                --chat--color--primary--shade-100: #B8005A;
                --chat--color--secondary: #2D1140;
                --chat--color-secondary-shade-50: #1A0A24;
                --chat--color-white: #ffffff;
                --chat--color-light: #FFF4FA;
                --chat--color-light-shade-50: #FBE0EF;
                --chat--color-light-shade-100: #F3C2DD;
                --chat--color-medium: #E8B8D4;
                --chat--color-dark: #2A1030;
                --chat--color-disabled: #D8B9CB;
                --chat--color-typing: #FF0082;

                /* --- Layout base --- */
                --chat--spacing: 1rem;
                --chat--border-radius: 14px;
                --chat--transition-duration: 0.2s;
                --chat--font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

                /* --- Ventana --- */
                --chat--window--width: 380px;
                --chat--window--height: 620px;
                --chat--window--border: 1px solid var(--chat--color-light-shade-50);
                --chat--window--border-radius: 22px;

                /* --- Encabezado --- */
                --chat--header--padding: 1.4rem 1.5rem;
                --chat--header--background: linear-gradient(135deg, #FF0082 0%, #B400D6 100%);
                --chat--header--color: #ffffff;
                --chat--header--border-top: none;
                --chat--header--border-bottom: none;
                --chat--heading--font-size: 1.35em;
                --chat--subtitle--font-size: 0.85em;
                --chat--subtitle--line-height: 1.5;

                /* --- Mensajes --- */
                --chat--message--font-size: 0.95rem;
                --chat--message--padding: 0.85rem 1.15rem;
                --chat--message--border-radius: 18px;
                --chat--message-line-height: 1.5;
                --chat--message--margin-bottom: 0.75rem;
                --chat--message--bot--background: #ffffff;
                --chat--message--bot--color: var(--chat--color-dark);
                --chat--message--bot--border: 1px solid #F3DCEC;
                --chat--message--user--background: linear-gradient(135deg, #FF0082, #FF4FA0);
                --chat--message--user--color: #ffffff;
                --chat--message--user--border: none;
                --chat--message--pre--background: rgba(255, 0, 130, 0.06);

                /* --- Botón flotante (toggle) --- */
                --chat--toggle--size: 60px;
                --chat--toggle--background: #FF0082;
                --chat--toggle--hover--background: #E0006F;
                --chat--toggle--active--background: #B8005A;
                --chat--toggle--color: #ffffff;

                /* --- Caja de texto / envío --- */
                --chat--textarea--height: 48px;
                --chat--input--container--background: #ffffff;
                --chat--input--container--border: 1px solid #F3C2DD;
                --chat--input--container--border-radius: 20px;
                --chat--input--button--border-radius: 14px;
                --chat--input--send--button--color: var(--chat--color--primary);
                --chat--input--send--button--background-hover: rgba(255, 0, 130, 0.08);
                --chat--input--send--button--color-hover: var(--chat--color--primary-shade-50);

                /* --- Botones internos (sugerencias / "empezar") --- */
                --chat--button--border-radius: 14px;
                --chat--button--background--primary: var(--chat--color--primary);
                --chat--button--color--primary: #ffffff;
                --chat--button--background--primary--hover: var(--chat--color--primary-shade-50);

                /* --- Cuerpo y pie --- */
                --chat--body--background: var(--chat--color-light);
                --chat--footer--background: var(--chat--color-light);
                --chat--footer--color: var(--chat--color-dark);
                --chat--footer--border-top: 1px solid var(--chat--color-light-shade-100);
            }

            /* Toques extra que las variables no cubren: sombra de la ventana,
               scrollbar a juego y un suave "respiro" en el botón flotante. */
            .chat-window-wrapper .chat-window {
                box-shadow: 0 20px 48px rgba(45, 17, 64, 0.35);
            }

            .chat-window-wrapper .chat-window-toggle {
                box-shadow: 0 8px 22px rgba(255, 0, 130, 0.45);
                animation: brisa-chat-pulse 2.6s ease-in-out infinite;
            }

            @keyframes brisa-chat-pulse {
                0%, 100% { box-shadow: 0 8px 22px rgba(255, 0, 130, 0.45); }
                50%      { box-shadow: 0 8px 30px rgba(255, 0, 130, 0.65), 0 0 0 7px rgba(255, 0, 130, 0.08); }
            }

            .chat-layout .chat-body::-webkit-scrollbar {
                width: 6px;
            }
            .chat-layout .chat-body::-webkit-scrollbar-thumb {
                background: #F3C2DD;
                border-radius: 10px;
            }

            /* =====================================================================
               Mobile: el chat pasa a ocupar toda la pantalla (como WhatsApp/IG),
               en vez de quedar como una ventanita chica difícil de usar con el
               pulgar. Cubre notch/safe-area, evita el zoom automático de iOS al
               tocar el input, y respeta el alto real del navegador móvil (la
               barra de direcciones que aparece/desaparece) usando dvh.
               ===================================================================== */
            @media (max-width: 600px) {
                :root {
                    --chat--window--width: 100vw;
                    --chat--window--height: 100vh;   /* fallback para navegadores viejos */
                    --chat--window--height: 100dvh;  /* alto real visible en mobile moderno */
                    --chat--window--border-radius: 0px;
                    --chat--window--border: none;
                    --chat--window--bottom: 0px;
                    --chat--window--right: 0px;
                    --chat--window--margin-bottom: 0px;
                    --chat--header--padding: 1.1rem 1.25rem env(safe-area-inset-top, 1.1rem);
                    --chat--heading--font-size: 1.15em;
                    --chat--message--font-size: 0.92rem;
                    --chat--toggle--size: 56px;
                    /* 16px evita que iOS Safari haga zoom solo al enfocar el textarea */
                    --chat--input--font-size: 16px;
                }

                .chat-window-wrapper .chat-window {
                    box-shadow: none;
                }

                /* El botón flotante (visible cuando el chat está cerrado) recupera
                   un margen propio para no quedar pegado al borde/notch */
                .chat-window-wrapper .chat-window-toggle {
                    margin-right: max(14px, env(safe-area-inset-right));
                    margin-bottom: max(14px, env(safe-area-inset-bottom));
                    -webkit-tap-highlight-color: transparent;
                }

                .chat-layout .chat-footer {
                    padding-bottom: env(safe-area-inset-bottom, 0px);
                }
            }

            /* Evita que, al hacer scroll hasta el final de los mensajes, el dedo
               siga arrastrando y haga rebotar la página de atrás (típico de iOS) */
            .chat-layout .chat-body {
                overscroll-behavior: contain;
            }

            @media (prefers-reduced-motion: reduce) {
                .chat-window-wrapper .chat-window-toggle {
                    animation: none;
                }
            }
        </style>
        <script type="module" defer>
            import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';

            // Use requestIdleCallback or setTimeout to defer chat initialization
            const initChat = () => {
                createChat({
                    webhookUrl: 'https://maaaaaa.app.n8n.cloud/webhook/423a7b0d-6424-4353-aa17-5af3a36bc2e2/chat',
                    showWelcomeScreen: true,
                    defaultLanguage: 'es',
                    initialMessages: [
                        '¡Hola! Soy el asistente virtual de BrisaNails. 💅',
                        '¿Te ayudo a reservar un turno o querés consultar los precios de nuestros servicios?'
                    ],
                    i18n: {
                        es: {
                            title: '¡Hola! 👋',
                            subtitle: 'Reservá tu turno o consultá precios al instante.',
                            footer: '',
                            getStarted: 'Nueva conversación',
                            inputPlaceholder: 'Escribí tu consulta aquí...',
                            closeButtonTooltip: 'Cerrar chat',
                        },
                    },
                });
            };

            if ('requestIdleCallback' in window) {
                requestIdleCallback(initChat);
            } else {
                setTimeout(initChat, 2000);
            }
        </script>
    </body>
    </html>
