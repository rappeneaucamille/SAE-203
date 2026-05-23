<?php
session_start();
session_unset();
session_destroy();

// On redirige en utilisant un chemin qui repart de la racine du serveur
header('Location: /SAE-203 - v2/index.php');
exit();
?>