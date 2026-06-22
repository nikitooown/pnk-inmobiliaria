<?php
/**
 * Headers de seguridad para la aplicación
 */

// Prevenir que el navegador detecte el tipo de contenido incorrectamente
header('X-Content-Type-Options: nosniff');

// Prevenir que el sitio sea embebido en iframes (excepto en el mismo dominio)
header('X-Frame-Options: SAMEORIGIN');

// Activar protección XSS en navegadores antiguos
header('X-XSS-Protection: 1; mode=block');

// Política de seguridad de contenido (CSP) básica
header("Content-Security-Policy: default-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com https://fonts.gstatic.com https://images.pexels.com https://maps.google.com https://wa.me https://www.facebook.com; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; img-src 'self' data: https:; connect-src 'self'; frame-src https://maps.google.com https://wa.me https://www.facebook.com;");

// Prevenir envío de referrer a sitios externos
header('Referrer-Policy: strict-origin-when-cross-origin');

// No almacenar en caché páginas sensibles
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
?>