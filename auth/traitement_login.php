<?php
require_once '../includes/db.php';
session_start(); // TRÈS IMPORTANT : Toujours au début pour gérer les sessions

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    // 1. Chercher chez les étudiants
    $stmt = $pdo->prepare("SELECT * FROM Etudiant WHERE identifiant = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user['pwd'])) {
        $_SESSION['user_id'] = $user['num_etudiant'];
        $_SESSION['identifiant'] = $user['identifiant']; // <-- ON AJOUTE ÇA
        $_SESSION['role'] = 'etudiant';
        header('Location: ../index.php');
        exit();
    }

    // 2. Chercher chez les enseignants
    $stmt = $pdo->prepare("SELECT * FROM Enseignant WHERE identifiant = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user['pwd'])) {
        $_SESSION['user_id'] = $user['id_ens'];
        $_SESSION['identifiant'] = $user['identifiant']; // <-- ON AJOUTE ÇA AUSSI
        $_SESSION['role'] = $user['fonctions'];
        header('Location: ../index.php');
        exit();
    }

    // Si rien n'est trouvé
    header('Location: ../index.php?error=1');
    exit();
}