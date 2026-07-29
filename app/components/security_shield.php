<?php
// Si estamos en entorno local (localhost / 127.0.0.1 / IP privada), no cargamos el escudo de bloqueo para permitir depurar con F12
$host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
$is_local = in_array(explode(':', $host)[0], ['localhost', '127.0.0.1', '::1']) || 
            (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));

if (!$is_local):
?>
<!-- ====================================================================== -->
<!-- ESCUDO DE SEGURIDAD FRONTEND - CUT LEVEL BARBERÍA                      -->
<!-- Bloquea Clic Derecho, Arrastre de Imágenes y Atajos de Inspección      -->
<!-- ====================================================================== -->
<script>
(function() {
    'use strict';
    
    // Verificación adicional en JS por si acaso se accede por file:// o localhost
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' || window.location.hostname === '') {
        return;
    }

    // 1. Bloquear el menú contextual (Clic Derecho) para proteger fotos y diseño
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    }, false);

    // 2. Bloquear arrastre de imágenes y vídeos (Drag & Drop al escritorio)
    document.addEventListener('dragstart', function(e) {
        if (e.target && (e.target.nodeName === 'IMG' || e.target.nodeName === 'VIDEO' || e.target.nodeName === 'A')) {
            e.preventDefault();
            return false;
        }
    }, false);

    // 3. Bloquear atajos de teclado para inspección de código y descarga
    document.addEventListener('keydown', function(e) {
        // Bloquear tecla F12
        if (e.key === 'F12' || e.keyCode === 123) {
            e.preventDefault();
            return false;
        }

        // Bloquear Ctrl+Shift+I / Cmd+Option+I (Inspeccionar), Ctrl+Shift+J (Consola), Ctrl+Shift+C (Inspector de elementos)
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && ['I', 'i', 'J', 'j', 'C', 'c'].includes(e.key)) {
            e.preventDefault();
            return false;
        }

        // Bloquear Ctrl+U / Cmd+U (Ver código fuente)
        if ((e.ctrlKey || e.metaKey) && ['U', 'u'].includes(e.key)) {
            e.preventDefault();
            return false;
        }

        // Bloquear Ctrl+S / Cmd+S (Guardar página web)
        if ((e.ctrlKey || e.metaKey) && ['S', 's'].includes(e.key)) {
            e.preventDefault();
            return false;
        }
    }, false);
})();
</script>
<?php endif; ?>
