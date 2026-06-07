<?php
// Activation des erreurs pour le débogage local
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Paramètres de connexion
$host = 'mysql-cuvillierrappeneau.alwaysdata.net';
$dbname = 'cuvillierrappeneau_gestion_stages_sae';
$user = 'cuvillierrappeneau';
$pass = 'projets.mmicb';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    // On active les erreurs SQL pour le développement
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // On force le mode FETCH_ASSOC par défaut pour simplifier le code
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}


?>