<?php
require_once '../includes/db.php';
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $fonction = $_POST['fonction'];

    $sql = "INSERT INTO Enseignant (identifiant, pwd, nom, prenom, fonctions) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([$email, $mdp, $nom, $prenom, $fonction]);
        echo "<div class='alert alert-success'>Compte créé ! <a href='../index.php'>Se connecter</a></div>";
    } catch(Exception $e) {
        echo "<div class='alert alert-danger'>Erreur : Email déjà utilisé.</div>";
    }
}
?>

<div class="container">
    <div class="card p-4 mx-auto shadow" style="max-width: 600px; border-top: 5px solid var(--dark-grey);">
        <h2 class="text-center mb-4">Espace Enseignant & Administration</h2>
        <form method="POST">
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
                <label class="form-label fw-bold">Fonction</label>
                <select name="fonction" class="form-select" required>
                    <option value="Enseignant standard">Enseignant standard</option>
                    <option value="Responsable stage">Responsable stage</option>
                    <option value="Jury de soutenance">Jury de soutenance</option>
                    <option value="Chef de département">Chef de département</option>
                    <option value="Administrateur">Administrateur</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Email Professionnel</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Mot de passe</label>
                <input type="password" name="mdp" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-dark w-100">Enregistrer le profil</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>