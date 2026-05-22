<?php
require_once '../../includes/db.php';

if ($_SESSION['role'] === 'etudiant' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $date_naiss = htmlspecialchars($_POST['date_naiss']);
    $tel = htmlspecialchars($_POST['tel']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $id_user = $_SESSION['user_id'];

    try {
        $sql = "UPDATE Etudiant SET date_naissance = ?, tel = ?, adresse_postale = ? WHERE num_etudiant = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$date_naiss, $tel, $adresse, $id_user]);

        header('Location: dashboard.php?status=success');
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de la mise à jour : " . $e->getMessage());
    }
}