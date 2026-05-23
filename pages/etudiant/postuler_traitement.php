<?php
require_once '../../includes/db.php';
session_start();

// Sécurité : on vérifie que l'utilisateur est un étudiant
if ($_SESSION['role'] !== 'etudiant') {
    header('Location: ../../index.php');
    exit();
}

if (isset($_GET['id_offre'])) {
    $id_offre = $_GET['id_offre'];
    $num_etud = $_SESSION['user_id'];

    try {
        // 1. On récupère les détails de l'offre pour remplir la table Recherche
        $queryOffre = $pdo->prepare("SELECT * FROM Offre WHERE id_offre = ?");
        $queryOffre->execute([$id_offre]);
        $offre = $queryOffre->fetch();

        if ($offre) {
            // 2. On insère une nouvelle ligne dans la table Recherche
            // On utilise 'contact' comme nom d'entreprise et 'intitule' comme offre consultee
            $sqlReq = "INSERT INTO Recherche (entreprise_contactee, offre_consultee, statut, date_recherche, reponses) 
                       VALUES (?, ?, 'En attente', NOW(), 0)";
            $stmt = $pdo->prepare($sqlReq);
            $stmt->execute([
                $offre['contact'], 
                $offre['intitule']
            ]);

            // 3. On récupère l'ID de la recherche qu'on vient de créer
            $id_recherche = $pdo->lastInsertId();

            // 4. On crée le lien dans la table de liaison Effectuer (Lien Etudiant <-> Recherche)
            $sqlEffectuer = "INSERT INTO Effectuer (num_etudiant, id_recherche) VALUES (?, ?)";
            $stmtEffectuer = $pdo->prepare($sqlEffectuer);
            $stmtEffectuer->execute([$num_etud, $id_recherche]);

            // 5. Redirection avec un message de succès
            header('Location: recherche.php?postule=success');
            exit();
        } else {
            header('Location: recherche.php?error=offre_introuvable');
            exit();
        }

    } catch (PDOException $e) {
        die("Erreur lors de la postulation : " . $e->getMessage());
    }
} else {
    // Si on arrive sur la page sans ID d'offre
    header('Location: recherche.php');
    exit();
}