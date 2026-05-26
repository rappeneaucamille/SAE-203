<?php
require_once '../includes/db.php';
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $mdp = $_POST['mdp'];
    $mdp_confirm = $_POST['mdp_confirm']; // On récupère la confirmation
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $fonction = $_POST['fonction'];

    // Vérification : les mots de passe correspondent-ils ?
    if ($mdp !== $mdp_confirm) {
        echo "<div class='alert alert-danger'>Erreur : Les mots de passe ne sont pas identiques.</div>";
    } else {
        // Si c'est bon, on hash le mot de passe
        $mdp_hashed = password_hash($mdp, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Enseignant (identifiant, pwd, nom, prenom, fonctions) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([$email, $mdp_hashed, $nom, $prenom, $fonction]);
            echo "<div class='alert alert-success'>Compte créé ! <a href='../index.php'>Se connecter</a></div>";
        } catch(Exception $e) {
            echo "<div class='alert alert-danger'>Erreur : Email déjà utilisé.</div>";
        }
    }
}
?>

<div class="container py-5">
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
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Mot de passe</label>
                    <input type="password" name="mdp" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Confirmer le mot de passe</label>
                    <input type="password" name="mdp_confirm" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn w-100 mt-2" style="background-color: #A7C7E7; color: #000000; border: none;">
                FINALISER MON INSCRIPTION
            </button>            <p class="mt-3 text-center">
                Déjà inscrit ? 
                <a href="../index.php" style="color: #000000; font-weight: bold;">Se connecter</a>
            </p>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>