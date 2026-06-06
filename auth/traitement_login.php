<?php
require_once '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $mdp = $_POST['mdp'];

    // 1. CHERCHER CHEZ LES ÉTUDIANTS
    $stmt = $pdo->prepare("SELECT * FROM Etudiant WHERE LOWER(identifiant) = LOWER(?)");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($mdp, $user['pwd'])) {
        if (isset($user['statut_compte']) && $user['statut_compte'] !== 'Validé') {
            header('Location: ../index.php?error=not_validated');
            exit();
        }

        $_SESSION['user_id'] = $user['num_etudiant'];
        $_SESSION['identifiant'] = $user['identifiant']; 
        $_SESSION['role'] = 'etudiant';
        
        header('Location: ../index.php'); 
        exit();
    }

    // 2. CHERCHER CHEZ LES ENSEIGNANTS / ADMINS
    $stmt = $pdo->prepare("SELECT * FROM Enseignant WHERE LOWER(identifiant) = LOWER(?)");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($mdp, $user['pwd'])) {
        if (isset($user['statut_compte']) && $user['statut_compte'] !== 'Validé') {
            header('Location: ../index.php?error=not_validated');
            exit();
        }

        $_SESSION['user_id'] = $user['id_ens'] ?? $user['identifiant'];
        $_SESSION['identifiant'] = $user['identifiant']; 
        $_SESSION['role'] = $user['fonctions'];

        header('Location: ../index.php');
        exit();
    }

    // Si rien n'est trouvé ou mot de passe incorrect
    header('Location: ../index.php?error=1');
    exit();
}
?>