<?php 
require_once 'includes/db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $fonction = $_POST['fonction'];

    $sql = "INSERT INTO Enseignant (identifiant, pwd, nom, prenom, fonctions) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([$email, $mdp, $nom, $prenom, $fonction]);
        echo "<div class='alert alert-success container'>Compte Enseignant créé ! <a href='index.php'>Se connecter</a></div>";
    } catch(Exception $e) { echo "<div class='alert alert-danger container'>Erreur : Email déjà utilisé.</div>"; }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h2 class="mb-4 text-center" style="color:var(--bleu-marine)">Inscription Enseignant</h2>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Nom</label><input type="text" name="nom" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label>Prénom</label><input type="text" name="prenom" class="form-control" required></div>
                    </div>
                    <div class="mb-3"><label>Email Professionnel</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>Mot de passe</label><input type="password" name="mdp" class="form-control" required></div>
                    <div class="mb-3">
                        <label>Fonction au sein du département</label>
                        <select name="fonction" class="form-select" required>
                            <option value="Responsable stage">Responsable stage</option>
                            <option value="Chef de département">Chef de département</option>
                            <option value="Jury de soutenance">Jury de soutenance</option>
                            <option value="Enseignant standard">Enseignant standard</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Créer mon compte enseignant</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>