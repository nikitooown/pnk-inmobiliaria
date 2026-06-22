<?php
/**
 * Configuración de timeout de sesión
 * Tiempo máximo de inactividad: 30 minutos (1800 segundos)
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir tiempo de timeout (30 minutos)
define('SESSION_TIMEOUT', 1800);

// Verificar si el usuario está autenticado y si la sesión ha expirado
if (isset($_SESSION['ultimo_acceso'])) {
    $tiempo_transcurrido = time() - $_SESSION['ultimo_acceso'];
    
    // Si ha pasado más tiempo del permitido, destruir la sesión
    if ($tiempo_transcurrido > SESSION_TIMEOUT) {
        // Guardar mensaje de sesión expirada
        $_SESSION = [];
        session_destroy();
        
        // Redirigir con mensaje de timeout
        header('Location: ../iniciosesion.php?error=timeout');
        exit();
    }
}

// Actualizar timestamp de último acceso
$_SESSION['ultimo_acceso'] = time();
?>