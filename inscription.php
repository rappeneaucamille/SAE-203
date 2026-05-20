<?php 
require_once 'includes/db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $num = $_POST['num_etudiant'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
    $promo = $_POST['promotion'];
    $td = $_POST['td'];
    $tp = $_POST['tp'];

    $sql = "INSERT INTO Etudiant (num_etudiant, identifiant, pwd, nom, prenom, promotion, groupe_TD, groupe_TP) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([$num, $email, $mdp, $nom, $prenom, $promo, $td, $tp]);
        echo "<div class='alert alert-success container'>Inscription réussie ! <a href='index.php'>Connectez-vous ici</a></div>";
    } catch(Exception $e) {
        echo "<div class='alert alert-danger container'>Erreur : Ce numéro ou email existe déjà.</div>";
    }
}
?>

<div class="container mb-5">
    <div class="card p-4 mx-auto" style="max-width: 600px;">
        <h2 class="text-center mb-4" style="color:var(--bleu-marine)">Inscription Étudiant</h2>
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3"><label>N° Étudiant</label><input type="number" name="num_etudiant" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label>Email Univ.</label><input type="email" name="email" class="form-control" required></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label>Nom</label><input type="text" name="nom" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label>Prénom</label><input type="text" name="prenom" class="form-control" required></div>
            </div>
            <div class="mb-3"><label>Mot de passe</label><input type="password" name="mdp" class="form-control" required></div>
            <div class="row">
                <div class="col-md-4 mb-3"><label>Promotion</label>
                    <select name="promotion" class="form-select"><option>MMI1</option><option>MMI2</option><option>MMI3</option></select>
                </div>
                <div class="col-md-4 mb-3"><label>Groupe TD</label><input type="text" name="td" class="form-control" placeholder="A1"></div>
                <div class="col-md-4 mb-3"><label>Groupe TP</label><input type="text" name="tp" class="form-control" placeholder="TP1"></div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Créer mon compte</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>