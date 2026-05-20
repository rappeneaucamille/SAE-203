<?php
require_once '../includes/db.php';

// On vérifie que l'utilisateur est bien un étudiant et qu'il a envoyé le formulaire
if ($_SESSION['role'] === 'etudiant' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Récupération des données du formulaire
    // On utilise htmlspecialchars pour éviter les failles XSS (important pour les points SAé)
    $date_naiss = htmlspecialchars($_POST['date_naiss']);
    $lieu_naiss = htmlspecialchars($_POST['lieu_naiss']);
    $tel = htmlspecialchars($_POST['tel']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $td = htmlspecialchars($_POST['td']);
    $tp = htmlspecialchars($_POST['tp']);
    $id_user = $_SESSION['user_id'];

    try {
        // 2. Préparation de la requête de mise à jour
        $sql = "UPDATE Etudiant 
                SET date_naissance = ?, 
                    lieu_naissance = ?, 
                    tel = ?, 
                    adresse_postale = ?, 
                    groupe_TD = ?, 
                    groupe_TP = ? 
                WHERE num_etudiant = ?";
        
        $stmt = $pdo->prepare($sql);
        
        // 3. Exécution avec les variables
        $stmt->execute([$date_naiss, $lieu_naiss, $tel, $adresse, $td, $tp, $id_user]);

        // 4. Redirection vers le profil avec un message de succès
        header('Location: dashboard_etudiant.php?status=success');
        exit();

    } catch (PDOException $e) {
        // En cas d'erreur SQL
        header('Location: dashboard_etudiant.php?status=error');
        exit();
    }
} else {
    // Si on essaie d'accéder au fichier sans passer par le formulaire
    header('Location: ../index.php');
    exit();
}
?>

<?php include '../includes/footer.php'; ?>