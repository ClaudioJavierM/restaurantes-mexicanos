// PWA Installation Handler
let deferredPrompt = null;

// Detectar cuando la app puede ser instalada
window.addEventListener('beforeinstallprompt', (e) => {
    // Prevenir el prompt automático
    e.preventDefault();
    deferredPrompt = e;

    // Mostrar botón de instalación personalizado
    const installButton = document.getElementById('pwa-install-btn');
    if (installButton) {
        installButton.classList.remove('hidden');
    }
});

// Función para instalar la PWA
window.installPWA = async function() {
    if (!deferredPrompt) {
        console.log('PWA ya está instalada o no está disponible');
        return;
    }

    // Mostrar el prompt de instalación
    deferredPrompt.prompt();

    // Esperar la decisión del usuario
    const { outcome } = await deferredPrompt.userChoice;

    console.log(`Usuario ${outcome === 'accepted' ? 'aceptó' : 'rechazó'} la instalación`);

    // Limpiar el prompt
    deferredPrompt = null;

    // Ocultar el botón
    const installButton = document.getElementById('pwa-install-btn');
    if (installButton) {
        installButton.classList.add('hidden');
    }
};

// Detectar cuando la app fue instalada con éxito
window.addEventListener('appinstalled', () => {
    console.log('PWA instalada con éxito!');
    deferredPrompt = null;
});

// Mostrar mensaje si ya está instalado
if (window.matchMedia('(display-mode: standalone)').matches) {
    console.log('La app ya está instalada y corriendo en modo standalone');
}
