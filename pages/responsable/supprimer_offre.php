<?php
require_once '../../includes/db.php';
session_start();

// Sécurité : on vérifie que c'est bien le responsable
if ($_SESSION['role'] !== 'Responsable stage') {
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