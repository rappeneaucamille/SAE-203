<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

if ($_SESSION['role'] !== 'etudiant') { header('Location: ../../index.php'); exit(); }

// On cherche le stage de l'étudiant avec les infos de l'entreprise
$stmt = $pdo->prepare("
    SELECT s.*, e.nom_ent, e.adresse_ent, m.nom_maitre, m.prenom_maitre 
    FROM Stage s
    JOIN Entreprise e ON s.id_ent = e.id_ent
    JOIN Maitre_stage m ON s.id_maitre = m.id_maitre
    WHERE s.num_etudiant = ?
");
$stmt->execute([$_SESSION['user_id']]);
$stage = $stmt->fetch();
?>

<div class="container">
    <h2 class="fw-bold mb-4">Détails de mon Stage</h2>

    <?php if (!$stage): ?>
        <div class="alert alert-warning">
            Vous n'avez pas encore de stage validé. <a href="recherche.php">Consulter les offres</a>.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-md-7">
                <div class="card p-4 shadow-sm">
                    <h5 class="text-primary"><i class="bi bi-building"></i> L'Entreprise</h5>
                    <hr>
                    <p><strong>Structure :</strong> <?= $stage['nom_ent'] ?></p>
                    <p><strong>Adresse :</strong> <?= $stage['adresse_ent'] ?></p>
                    <p><strong>Missions :</strong><br><?= nl2br($stage['description_mission']) ?></p>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card p-4 shadow-sm border-start border-4 border-info">
                    <h5><i class="bi bi-person-badge"></i> Encadrement</h5>
                    <hr>
                    <p><strong>Maître de stage :</strong> <?= $stage['nom_maitre'] ?> <?= $stage['prenom_maitre'] ?></p>
                    <p><strong>Convention :</strong> <span class="badge bg-success">Signée</span></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>