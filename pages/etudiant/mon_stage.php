<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

$id_etud = $_SESSION['user_id'];

// On vérifie si une démarche est VALIDÉE
$sql = "SELECT r.* FROM Recherche r 
        JOIN Effectuer ef ON r.id_recherche = ef.id_recherche 
        WHERE ef.num_etudiant = ? AND r.statut = 'Validée' LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_etud]);
$stageValide = $stmt->fetch();
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4" style="color: var(--mmi-blue);">Suivi de ma convention</h2>

    <?php if($stageValide): ?>
        <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 20px; background-color: var(--pastel-blue);">
            <div class="mb-4"><i class="bi bi-briefcase-fill text-success" style="font-size: 5rem;"></i></div>
            <h2 class="fw-bold">Félicitations !</h2>
            <p class="fs-5">Votre stage chez <strong><?= $stageValide['entreprise_contactee'] ?></strong> est validé par le responsable.</p>
            <hr class="w-50 mx-auto">
            <div class="mt-4">
                <p><strong>Missions :</strong> <?= $stageValide['offre_consultee'] ?></p>
                <span class="badge bg-success p-2 px-4">Convention en cours de signature</span>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 20px; background-color: #f8f9fa;">
            <div class="mb-4"><i class="bi bi-hourglass-split text-warning" style="font-size: 5rem;"></i></div>
            <h3>Pas encore de stage validé</h3>
            <p class="text-muted">Dès qu'une de vos démarches sera validée par le responsable, les informations de votre stage apparaîtront ici.</p>
            <a href="recherche.php" class="btn btn-primary mt-3">Voir mes candidatures</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>