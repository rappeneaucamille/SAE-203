<?php
require_once '../../includes/db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "UPDATE Etudiant SET nom=?, prenom=?, tel=?, adresse=?, promotion=?, groupe_TD=?, groupe_TP=? WHERE num_etudiant=?";
    $pdo->prepare($sql)->execute([
        $_POST['nom'], $_POST['prenom'], $_POST['tel'], $_POST['adresse'], 
        $_POST['promotion'], $_POST['groupe_td'], $_POST['groupe_tp'], $_SESSION['user_id']
    ]);
    header('Location: dashboard.php?msg=success');
}