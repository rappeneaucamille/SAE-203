<?php
session_start();
require_once '../../includes/db.php';

// Sécurité : Vérification que l'utilisateur est bien connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_stage'])) {
    $id_stage = $_POST['id_stage'];

    // Rassemblement de toutes les données saisies dans le formulaire
    $convention_data = [
        'telephone_etudiant'   => trim(htmlspecialchars($_POST['telephone_etudiant'] ?? '')),
        'date_naissance'       => trim(htmlspecialchars($_POST['date_naissance'] ?? '')),
        'lieu_naissance'       => trim(htmlspecialchars($_POST['lieu_naissance'] ?? '')),
        'adresse_etudiant'     => trim(htmlspecialchars($_POST['adresse_etudiant'] ?? '')),
        'siret'                => trim(htmlspecialchars($_POST['siret'] ?? '')),
        'representant_legal'   => trim(htmlspecialchars($_POST['representant_legal'] ?? '')),
        'heures_totales'       => trim(htmlspecialchars($_POST['heures_totales'] ?? '')),
        'modalite_presence'    => trim(htmlspecialchars($_POST['modalite_presence'] ?? '')),
        'service_affectation'  => trim(htmlspecialchars($_POST['service_affectation'] ?? '')),
        'horaires_travail'     => trim(htmlspecialchars($_POST['horaires_travail'] ?? '')),
        'objectifs_pedagogiques'=> trim(htmlspecialchars($_POST['objectifs_pedagogiques'] ?? '')),
        'montant_gratification'=> trim(htmlspecialchars($_POST['montant_gratification'] ?? '')),
        'modalite_versement'   => trim(htmlspecialchars($_POST['modalite_versement'] ?? ''))
    ];

    // Encodage au format JSON pour stockage propre dans la colonne 'description'
    $json_data = json_encode($convention_data, JSON_UNESCAPED_UNICODE);

    try {
        // Mise à jour de la table Stage
        $query = "UPDATE Stage SET description = ? WHERE id_stage = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$json_data, $id_stage]);

        // Redirection vers la page mon_stage.php avec un indicateur de succès
        header("Location: mon_stage.php?status=saved");
        exit();

    } catch (PDOException $e) {
        // En cas d'erreur SQL, affichage propre pour débugger
        die("Erreur lors de l'enregistrement de la convention : " . $e->getMessage());
    }
} else {
    // Si accès direct au fichier sans POST
    header("Location: mon_stage.php");
    exit();
}