<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

if ($_SESSION['role'] !== 'Chef de département') { header('Location: ../../index.php'); exit(); }

// Requête pour voir l'état par promotion
$stats_promo = $pdo->query("
    SELECT promotion, COUNT(*) as total, 
    SUM(CASE WHEN num_etudiant IN (SELECT num_etudiant FROM Stage) THEN 1 ELSE 0 END) as avec_stage
    FROM Etudiant GROUP BY promotion
")->fetchAll();
?>

<div class="container">
    <h2 class="fw-bold mb-4">Vision Globale Département MMI</h2>

    <div class="row g-4">
        <?php foreach($stats_promo as $s): 
            $percent = ($s['total'] > 0) ? round(($s['avec_stage'] / $s['total']) * 100) : 0;
        ?>
        <div class="col-md-4">
            <div class="card p-4 shadow-sm border-0">
                <h5 class="fw-bold text-primary"><?= $s['promotion'] ?></h5>
                <p class="mb-1 small text-muted">Taux d'affectation : <?= $percent ?>%</p>
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: <?= $percent ?>%"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <span><strong><?= $s['avec_stage'] ?></strong> placés</span>
                    <span><strong><?= $s['total'] ?></strong> total</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card mt-5 p-4">
        <h5>Dernières entreprises partenaires</h5>
        <table class="table table-sm mt-3">
            <thead><tr><th>Entreprise</th><th>Ville</th><th>Nombre de stagiaires</th></tr></thead>
            <tbody>
                <tr><td>Ubisoft</td><td>Saint-Mandé</td><td>3</td></tr>
                <tr><td>Publicis</td><td>Paris</td><td>2</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>