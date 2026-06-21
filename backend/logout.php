<?php
// backend/logout.php
session_start();
session_destroy();

// Eliminar cookies de "recuérdame"
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
    setcookie('remember_tipo', '', time() - 3600, '/');
}

header('Location: ../iniciosesion.php');
exit;
?>
