<?php
require_once '../includes/db.php';
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $num = htmlspecialchars($_POST['num_etudiant']);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
    $promo = $_POST['promotion'];

    $sql = "INSERT INTO Etudiant (num_etudiant, identifiant, pwd, nom, prenom, promotion) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([$num, $email, $mdp, $nom, $prenom, $promo]);
        echo "<div class='alert alert-success'>Inscription réussie ! <a href='../index.php'>Connectez-vous</a></div>";
    } catch(Exception $e) {
        echo "<div class='alert alert-danger'>Erreur : Ce numéro ou email est déjà utilisé.</div>";
    }
}
?>

<div class="container">
    <div class="card p-4 mx-auto shadow" style="max-width: 600px; border-top: 5px solid var(--mmi-blue);">
        <h2 class="text-center mb-4">Inscription Étudiant</h2>
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">N° Étudiant</label>
                    <input type="number" name="num_etudiant" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Promotion</label>
                    <select name="promotion" class="form-select">
                        <option>MMI1</option>
                        <option>MMI2</option>
                        <option>MMI3</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nom</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Prénom</label>
                    <input type="text" name="prenom" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Email Universitaire</label>
                <input type="email" name="email" class="form-control" placeholder="@univ-eiffel.fr" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Mot de passe</label>
                <input type="password" name="mdp" class="form-control" required