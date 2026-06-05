<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité : Seul l'administrateur peut accéder à cette page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $fonction = $_POST['fonction'];
    $mdp_brut = $_POST['mdp'];

    // 1. VÉRIFICATION DU FORMAT DE L'EMAIL (Compatible PHP 7 et PHP 8)
    $domaine = '@univ-eiffel.fr';
    $longueur_domaine = strlen($domaine);
    $fin_email = substr($email, -$longueur_domaine);

    if ($fin_email !== $domaine) {
        $error = "L'adresse e-mail doit obligatoirement se terminer par @univ-eiffel.fr";
    } else {
        try {
            // 2. VÉRIFICATION DE L'EXISTENCE DU COMPTE
            $stmt = $pdo->prepare("SELECT * FROM Enseignant WHERE LOWER(identifiant) = LOWER(?)");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = "Cet e-mail / identifiant est déjà utilisé par un autre enseignant.";
            } else {
                // Hachage propre et sécurisé du mot de passe
                $mdp = password_hash($mdp_brut, PASSWORD_DEFAULT);

                // 3. INSERTION EN BDD AVEC LE STATUT 'VALIDÉ'
                // Attention : vérifie bien que ta colonne s'appelle 'pwd' dans ta table Enseignant !
                $sql = "INSERT INTO Enseignant (identifiant, pwd, nom, prenom, fonctions, statut_compte) VALUES (?, ?, ?, ?, ?, 'Validé')";
                $pdo->prepare($sql)->execute([$email, $mdp, $nom, $prenom, $fonction]);
                
                // Redirection immédiate vers la page de gestion avec succès
                header('Location: gestion.php?status=added');
                exit();
            }
        } catch (Exception $e) {
            $error = "Une erreur est survenue lors de l'insertion : " . $e->getMessage();
        }
    }
}
?>

<div class="container py-5">
    <div class="card p-4 mx-auto shadow border-0" style="max-width: 500px;">
        <h4 class="fw-bold mb-4">Ajouter un Enseignant</h4>
        
        <?php if ($error): ?>
            <div class="alert alert-danger border-0 shadow-sm mb-3"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" required value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" required value="<?= isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '' ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email (Identifiant)</label>
                <input type="email" name="email" class="form-control" placeholder="exemple@univ-eiffel.fr" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Fonction / Rôle</label>
                <select name="fonction" class="form-select">
                    <option value="Enseignant standard">Enseignant standard</option>
                    <option value="Jury de soutenance">Jury de soutenance</option>
                    <option value="Responsable stage">Responsable stage</option>
                    <option value="Chef de département">Chef de département</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Mot de passe provisoire</label>
                <input type="password" name="mdp" class="form-control" required>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Créer l'enseignant</button>
                <a href="gestion.php" class="btn btn-light border w-100">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>