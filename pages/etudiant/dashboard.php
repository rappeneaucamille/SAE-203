<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité : on vérifie que c'est bien un étudiant
if ($_SESSION['role'] !== 'etudiant') {
    header('Location: ../../index.php');
    exit();
}

// On récupère les infos de l'étudiant via son ID stocké en session
$stmt = $pdo->prepare("SELECT * FROM Etudiant WHERE num_etudiant = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Mon Profil & Scolarité</h2>
        <span class="badge-role">Étudiant <?= $user['promotion'] ?></span>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success">Profil mis à jour avec succès !</div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h5 class="text-primary mb-3">Informations Personnelles</h5>
                <p><strong>Nom :</strong> <?= strtoupper($user['nom']) ?></p>
                <p><strong>Prénom :</strong> <?= $user['prenom'] ?></p>
                <p><strong>Email :</strong> <?= $user['identifiant'] ?></p>
                <p><strong>Téléphone :</strong> <?= $user['tel'] ?? '<span class="text-muted">Non renseigné</span>' ?></p>
                <p><strong>Adresse :</strong> <?= $user['adresse_postale'] ?? '<span class="text-muted">Non renseignée</span>' ?></p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4 h-100 border-start border-primary border-4">
                <h5 class="mb-3">Compléter mon profil</h5>
                <form action="update_full_profil.php" method="POST" class="row g-3">
                    <div class="col-md-6"><label>Nom</label><input type="text" name="nom" class="form-control" value="<?= $user['nom'] ?>"></div>
                    <div class="col-md-6"><label>Prénom</label><input type="text" name="prenom" class="form-control" value="<?= $user['prenom'] ?>"></div>
                    <div class="col-md-6"><label>Téléphone</label><input type="text" name="tel" class="form-control" value="<?= $user['tel'] ?>"></div>
                    <div class="col-md-6"><label>Adresse</label><input type="text" name="adresse" class="form-control" value="<?= $user['adresse'] ?>"></div>
                    <div class="col-md-4"><label>Promotion</label><input type="text" name="promotion" class="form-control" value="<?= $user['promotion'] ?>"></div>
                    <div class="col-md-4"><label>Groupe TD</label><input type="text" name="groupe_td" class="form-control" value="<?= $user['groupe_TD'] ?>"></div>
                    <div class="col-md-4"><label>Groupe TP</label><input type="text" name="groupe_tp" class="form-control" value="<?= $user['groupe_TP'] ?>"></div>
                    <button type="submit" class="btn btn-primary mt-3">Mettre à jour mon profil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>