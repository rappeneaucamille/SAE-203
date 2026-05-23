<?php
require_once '../includes/db.php';
include '../includes/header.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données selon ton SQL
    $num = htmlspecialchars($_POST['num_etudiant']);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $identifiant = htmlspecialchars($_POST['email']); // On utilise l'email comme identifiant
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
    $promo = $_POST['promotion'];
    $tel = htmlspecialchars($_POST['tel']);
    $date_naiss = $_POST['date_naiss'];
    $groupe_td = $_POST['groupe_td'];

    // Requête SQL complète correspondant à ta table Etudiant
    $sql = "INSERT INTO Etudiant (num_etudiant, identifiant, pwd, nom, prenom, tel, date_naiss, promotion, groupe_TD) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([$num, $identifiant, $mdp, $nom, $prenom, $tel, $date_naiss, $promo, $groupe_td]);
        $message = "<div class='alert alert-success shadow-sm'>✨ Inscription réussie ! <a href='../index.php' class='fw-bold'>Connectez-vous ici</a></div>";
    } catch(Exception $e) {
        $message = "<div class='alert alert-danger shadow-sm'>❌ Erreur : Ce numéro étudiant ou cet email est déjà enregistré.</div>";
    }
}
?>

<div class="container py-5">
    <div class="card mx-auto shadow-lg border-0" style="max-width: 700px; border-radius: 15px; overflow: hidden;">
        <div class="p-4 text-white text-center" style="background-color: var(--mmi-blue);">
            <h2 class="mb-0 fw-bold">Rejoindre MMI Stages</h2>
            <p class="small opacity-75">Créez votre compte étudiant en quelques secondes</p>
        </div>

        <div class="card-body p-4" style="background-color: var(--pastel-blue);">
            <?= $message ?>

            <form method="POST" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nom</label>
                    <input type="text" name="nom" class="form-control" placeholder="Ex: MARTIN" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Prénom</label>
                    <input type="text" name="prenom" class="form-control" placeholder="Ex: Julie" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">N° Étudiant</label>
                    <input type="number" name="num_etudiant" class="form-control" placeholder="8 chiffres" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Promotion</label>
                    <select name="promotion" class="form-select">
                        <option value="MMI1">MMI 1</option>
                        <option value="MMI2">MMI 2</option>
                        <option value="MMI3">MMI 3</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Groupe TD</label>
                    <select name="groupe_td" class="form-select">
                        <option value="A1">A1</option>
                        <option value="A2">A2</option>
                        <option value="B1">B1</option>
                        <option value="B2">B2</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Date de naissance</label>
                    <input type="date" name="date_naiss" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Téléphone</label>
                    <input type="tel" name="tel" class="form-control" placeholder="06XXXXXXXX">
                </div>

                <div class="col-12 mt-4 p-3 rounded" style="background-color: var(--pastel-purple);">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Universitaire (Identifiant)</label>
                        <input type="email" name="email" class="form-control" placeholder="prenom.nom@edu.univ-eiffel.fr" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Mot de passe</label>
                        <input type="password" name="mdp" class="form-control" required>
                    </div>
                </div>

                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-lg text-white w-100 shadow-sm" style="background-color: var(--mmi-blue); border-radius: 10px;">
                        Créer mon compte
                    </button>
                    <p class="mt-3 mb-0">Déjà inscrit ? <a href="../index.php" style="color: var(--mmi-blue);">Connectez-vous</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>