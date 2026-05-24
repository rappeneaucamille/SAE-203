<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

if ($_SESSION['role'] !== 'Administrateur') exit();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $num = $_POST['num_etudiant'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $promo = $_POST['promotion'];
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO Etudiant (num_etudiant, nom, prenom, email, mdp, promotion) VALUES (?, ?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([$num, $nom, $prenom, $email, $mdp, $promo]);
    header('Location: gestion.php?status=added');
}
?>

<div class="container py-5">
    <div class="card p-4 mx-auto shadow border-0" style="max-width: 500px;">
        <h4 class="fw-bold mb-4">Ajouter un Étudiant</h4>
        <form method="POST">
            <div class="mb-3"><label>Numéro Étudiant</label><input type="text" name="num_etudiant" class="form-control" required></div>
            <div class="mb-3"><label>Nom</label><input type="text" name="nom" class="form-control" required></div>
            <div class="mb-3"><label>Prénom</label><input type="text" name="prenom" class="form-control" required></div>
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3">
                <label>Promotion</label>
                <select name="promotion" class="form-select">
                    <option value="MMI1">MMI1</option>
                    <option value="MMI2">MMI2</option>
                    <option value="MMI3">MMI3</option>
                </select>
            </div>
            <div class="mb-3"><label>Mot de passe provisoire</label><input type="password" name="mdp" class="form-control" required></div>
            <button type="submit" class="btn btn-primary w-100">Créer l'étudiant</button>
        </form>
    </div>
</div>