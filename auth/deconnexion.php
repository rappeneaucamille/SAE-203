<?php
session_start();
session_unset();
session_destroy();
// On redirige vers l'index à la racine
header('Location: ../index.php');
exit();
?>