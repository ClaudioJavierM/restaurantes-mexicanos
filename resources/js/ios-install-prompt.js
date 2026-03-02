// iOS Install Prompt Banner
// Detecta Safari iOS y muestra instrucciones de instalación

(function() {
    // Detectar si es iOS Safari
    function isIOS() {
        const userAgent = window.navigator.userAgent.toLowerCase();
        return /iphone|ipad|ipod/.test(userAgent);
    }

    // Detectar si es Safari (no Chrome en iOS)
    function isSafari() {
        const userAgent = window.navigator.userAgent.toLowerCase();
        return isIOS() && !userAgent.includes('crios') && !userAgent.includes('fxios');
    }

    // Verificar si ya está instalado (standalone mode)
    function isStandalone() {
        return ('standalone' in window.navigator) && (window.navigator.standalone);
    }

    // Verificar si el usuario ya cerró el banner
    function bannerWasDismissed() {
        return localStorage.getItem('ios-install-banner-dismissed') === 'true';
    }

    // Crear el banner HTML
    function createBanner() {
        const isSpanish = document.documentElement.lang === 'es';

        const banner = document.createElement('div');
        banner.id = 'ios-install-banner';
        banner.className = 'ios-install-banner';
        banner.innerHTML = `
            <div class="ios-banner-content">
                <button class="ios-banner-close" aria-label="${isSpanish ? 'Cerrar' : 'Close'}">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <div class="ios-banner-icon">
                    <img src="/pwa-192x192.png" alt="FMR" width="48" height="48">
                </div>

                <div class="ios-banner-text">
                    <h3>${isSpanish ? 'Instala FMR en tu iPhone' : 'Install FMR on your iPhone'}</h3>
                    <div class="ios-banner-steps">
                        <span class="ios-step">
                            ${isSpanish ? '1. Toca' : '1. Tap'}
                            <svg class="ios-share-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"></path>
                            </svg>
                        </span>
                        <span class="ios-step">
                            ${isSpanish ? '2. "Agregar a Pantalla de Inicio"' : '2. "Add to Home Screen"'}
                        </span>
                    </div>
                </div>
            </div>
        `;

        return banner;
    }

    // Mostrar banner
    function showBanner() {
        // No mostrar si ya está instalado
        if (isStandalone()) {
            return;
        }

        // No mostrar si no es Safari iOS
        if (!isSafari()) {
            return;
        }

        // No mostrar si ya fue cerrado
        if (bannerWasDismissed()) {
            return;
        }

        // Esperar a que el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', insertBanner);
        } else {
            insertBanner();
        }
    }

    // Insertar banner en el DOM
    function insertBanner() {
        const banner = createBanner();
        document.body.appendChild(banner);

        // Animar entrada después de un pequeño delay
        setTimeout(() => {
            banner.classList.add('show');
        }, 500);

        // Agregar evento al botón de cerrar
        const closeBtn = banner.querySelector('.ios-banner-close');
        closeBtn.addEventListener('click', closeBanner);
    }

    // Cerrar banner
    function closeBanner() {
        const banner = document.getElementById('ios-install-banner');
        if (banner) {
            banner.classList.remove('show');

            setTimeout(() => {
                banner.remove();
            }, 300);

            // Guardar que fue cerrado
            localStorage.setItem('ios-install-banner-dismissed', 'true');
        }
    }

    // Inicializar
    showBanner();
})();
