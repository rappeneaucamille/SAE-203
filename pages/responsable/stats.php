<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// On calcule le pourcentage de réussite
$total = $pdo->query("SELECT COUNT(*) FROM Etudiant")->fetchColumn();
$valides = $pdo->query("SELECT COUNT(*) FROM Stage")->fetchColumn();
$pourcentage = ($total > 0) ? round(($valides / $total) * 100) : 0;
?>

<div class="container">
    <h2 class="fw-bold mb-4">Statistiques du Département MMI</h2>
    
    <div class="card p-5 shadow-sm text-center">
        <h4>Avancement des recherches de stage</h4>
        <div class="progress my-4" style="height: 40px; font-size: 1.2rem;">
            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pourcentage ?>%;" aria-valuenow="<?= $pourcentage ?>" aria-valuemin="0" aria-valuemax="100">
                <?= $pourcentage ?>% des étudiants sont placés
            </div>
        </div>
        <p class="text-muted">Objectif : 100% avant le début des stages en Avril.</p>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>