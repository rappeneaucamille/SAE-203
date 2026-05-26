<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : On autorise le Responsable OU l'Admin
if ($_SESSION['role'] !== 'Responsable stage' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération de tous les champs
    $intitule    = $_POST['titre'];
    $contact     = $_POST['ent'];
    $description = $_POST['desc'];
    $competences = $_POST['competences'];
    $lieu        = $_POST['lieu'];
    $dates       = $_POST['dates'];
    $remu        = $_POST['remu'];
    $promo       = $_POST['promo'];
    $annee       = $_POST['annee'];

    try {
        // Préparation de l'insertion avec tous les champs demandés
        $sql = "INSERT INTO Offre (intitule, description, contact, remuneration, dates, lieu, competences) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$intitule, $description, $contact, $remu, $dates, $lieu, $competences]);

        // Redirection vers le dashboard après succès
        header('Location: dashboard.php?status=success');
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de l'enregistrement de l'offre : " . $e->getMessage());
    }
}