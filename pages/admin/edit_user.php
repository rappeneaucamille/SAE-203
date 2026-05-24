<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

$id = $_GET['id'];
$type = $_GET['type'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    
    if ($type == 'prof') {
        $fonction = $_POST['fonction'];
        $stmt = $pdo->prepare("UPDATE Enseignant SET nom = ?, prenom = ?, fonctions = ? WHERE identifiant = ?");
        $stmt->execute([$nom, $prenom, $fonction, $id]);
    } else {
        $promo = $_POST['promo'];
        $stmt = $pdo->prepare("UPDATE Etudiant SET nom = ?, prenom = ?, promotion = ? WHERE num_etudiant = ?");
        $stmt->execute([$nom, $prenom, $promo, $id]);
    }
    echo "<script>window.location.href='gestion.php?status=updated';</script>";
}

// Récupération
$u = ($type == 'prof') ? 
    $pdo->prepare("SELECT * FROM Enseignant WHERE identifiant = ?") : 
    $pdo->prepare("SELECT * FROM Etudiant WHERE num_etudiant = ?");
$u->execute([$id]);
$user = $u->fetch();
?>

<div class="container py-5">
    <div class="card p-4 mx-auto shadow border-0" style="max-width: 500px;">
        <h4 class="fw-bold mb-4">Modifier le profil : <?= $type == 'prof' ? 'Staff' : 'Étudiant' ?></h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= $user['nom'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?= $user['prenom'] ?>" required>
            </div>

            <?php if ($type == 'prof'): ?>
                <div class="mb-3">
                    <label class="form-label">Rôle / Fonction</label>
                    <select name="fonction" class="form-select">
                        <option value="Enseignant standard" <?= $user['fonctions'] == 'Enseignant standard' ? 'selected' : '' ?>>Enseignant standard</option>
                        <option value="Responsable stage" <?= $user['fonctions'] == 'Responsable stage' ? 'selected' : '' ?>>Responsable stage</option>
                        <option value="Chef de département" <?= $user['fonctions'] == 'Chef de département' ? 'selected' : '' ?>>Chef de département</option>
                        <option value="Jury de soutenance" <?= $user['fonctions'] == 'Jury de soutenance' ? 'selected' : '' ?>>Jury de soutenance</option>
                        <option value="Administrateur" <?= $user['fonctions'] == 'Administrateur' ? 'selected' : '' ?>>Administrateur</option>
                    </select>
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <label class="form-label">Promotion</label>
                    <select name="promo" class="form-select">
                        <option value="MMI1" <?= $user['promotion'] == 'MMI1' ? 'selected' : '' ?>>MMI 1</option>
                        <option value="MMI2" <?= $user['promotion'] == 'MMI2' ? 'selected' : '' ?>>MMI 2</option>
                        <option value="MMI3" <?= $user['promotion'] == 'MMI3' ? 'selected' : '' ?>>MMI 3</option>
                    </select>
                </div>
            <?php endif; ?>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
                <a href="gestion.php" class="btn btn-light border w-100">Annuler</a>
            </div>
        </form>
    </div>
</div>