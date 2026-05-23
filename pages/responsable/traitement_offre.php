<?php
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On récupère les 4 champs du formulaire
    $intitule    = $_POST['intitule'];
    $contact     = $_POST['contact'];
    $lieu        = $_POST['lieu'];
    $description = $_POST['description'];

    try {
        // On prépare l'insertion avec les 4 colonnes de ta table SQL
        $sql = "INSERT INTO Offre (intitule, contact, lieu, description) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$intitule, $contact, $lieu, $description]);

        // Redirection vers le dashboard avec succès
        header('Location: dashboard.php?status=success');
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
}