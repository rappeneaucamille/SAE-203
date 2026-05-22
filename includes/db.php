<?php
// On lance la session sur toutes les pages du site
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Paramètres de connexion
$host = 'localhost';
$dbname = 'gestion_stages_sae';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    // On active les erreurs SQL pour le développement
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>