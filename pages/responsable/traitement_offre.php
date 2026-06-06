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
    $contact     = trim($_POST['ent']); 
    $description = $_POST['desc'];
    $competences = $_POST['competences'];
    $lieu        = $_POST['lieu'];
    $dates       = $_POST['dates'];
    $remu        = $_POST['remu'];
    $promo       = $_POST['promo'];
    $annee       = $_POST['annee'];

    try {
        $pdo->beginTransaction();

        // 1. VERIFICATION / INSERTION DANS LA TABLE ENTREPRISE
        $stmtCheckEnt = $pdo->prepare("SELECT id_ent FROM entreprise WHERE nom = ?");
        $stmtCheckEnt->execute([$contact]);
        $entreprise = $stmtCheckEnt->fetch();

        if ($entreprise) {
            // L'entreprise existe déjà
            $id_ent = $entreprise['id_ent'];
        } else {
            // L'entreprise n'existe pas, on la crée à la volée
            // On utilise $lieu pour l'adresse et $contact pour initialiser le nom
            $stmtInsEnt = $pdo->prepare("INSERT INTO entreprise (nom, adresse, contact) VALUES (?, ?, ?)");
            $stmtInsEnt->execute([$contact, $lieu, $contact]);
            $id_ent = $pdo->lastInsertId();
        }

        // 2. INSERTION DE L'OFFRE AU CATALOGUE
        $sql = "INSERT INTO Offre (intitule, description, contact, remuneration, dates, lieu, competences) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$intitule, $description, $contact, $remu, $dates, $lieu, $competences]);

        $pdo->commit();

        // Redirection vers le dashboard après succès
        header('Location: dashboard.php?status=success');
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Erreur lors de l'enregistrement de l'offre : " . $e->getMessage());
    }
}