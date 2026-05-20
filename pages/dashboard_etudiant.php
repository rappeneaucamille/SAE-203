<?php 
require_once '../includes/db.php';
include '../includes/header.php';

if ($_SESSION['role'] !== 'etudiant') header('Location: ../index.php');

$stmt = $pdo->prepare("SELECT * FROM Etudiant WHERE num_etudiant = ?");
$stmt->execute([$_SESSION['user_id']]);
$u = $stmt->fetch();
?>
<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Profil & Situation Actuelle</h2>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h5>Informations Personnelles</h5><hr>
                <p><strong>Nom :</strong> <?= $u['nom'] ?></p>
                <p><strong>Prénom :</strong> <?= $u['prenom'] ?></p>
                <p><strong>Né(e) le :</strong> <?= $u['date_naissance'] ?? 'Non renseigné' ?> à <?= $u['lieu_naissance'] ?? '...' ?></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h5>Scolarité</h5><hr>
                <p><strong>N° Étudiant :</strong> <?= $u['num_etudiant'] ?></p>
                <p><strong>Promotion :</strong> <?= $u['promotion'] ?></p>
                <p><strong>Groupe :</strong> <?= $u['groupe_TD'] ?> / <?= $u['groupe_TP'] ?></p>
            </div>
        </div>
        <div class="col-12">
            <div class="card p-4">
                <h5>Coordonnées</h5><hr>
                <p><strong>Email :</strong> <?= $u['identifiant'] ?></p>
                <p><strong>Téléphone :</strong> <?= $u['tel'] ?? 'Non renseigné' ?></p>
                <p><strong>Adresse :</strong> <?= $u['adresse_postale'] ?? 'Non renseignée' ?></p>
                <button class="btn btn-primary btn-sm">Modifier mes coordonnées</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>