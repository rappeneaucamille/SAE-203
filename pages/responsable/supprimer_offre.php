<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : On autorise le Responsable OU l'Admin
if ($_SESSION['role'] !== 'Responsable stage' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}


// On vérifie qu'un ID est bien transmis
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_offre = $_GET['id'];

    try {
        // On supprime d'abord les liaisons si tu as une table 'avoir' ou 'postuler'
        // Sinon, on supprime directement l'offre
        $stmt = $pdo->prepare("DELETE FROM Offre WHERE id_offre = ?");
        $stmt->execute([$id_offre]);

        // Redirection vers la page des offres avec un message de succès
        header('Location: offres.php?delete=success');
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    // Si pas d'ID, on repart sur la liste
    header('Location: offres.php');
    exit();
}