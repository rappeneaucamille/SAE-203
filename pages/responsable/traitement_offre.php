<?php
require_once '../../includes/db.php';

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
        // J'utilise les colonnes de ta table SQL (intitule, description, contact, remuneration, dates, lieu, competences)
        // J'ajoute promotion et annee si tu as ces colonnes, sinon on les concatène dans la description
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