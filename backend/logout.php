<?php
// backend/logout.php
session_start();
session_destroy();

// Eliminar cookies de "recuérdame" si existen
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}
if (isset($_COOKIE['remember_tipo'])) {
    setcookie('remember_tipo', '', time() - 3600, '/');
}

header('Location: ../iniciosesion.php');
exit;
?>