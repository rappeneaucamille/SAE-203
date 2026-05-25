<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'etudiant') {
    header('Location: ../../index.php');
    exit();
}

// On récupère l'identifiant (ex: rap@rap) qui est maintenant bien dans la session
$etudiant_id = $_SESSION['identifiant'] ?? ''; 

$soutenance = false;
if (!empty($etudiant_id)) {
    // On cherche dans ta table soutenance
    $stmt = $pdo->prepare("SELECT note_soutenance, note_rapport, date_soutenance FROM soutenance WHERE etudiant = ?");
    $stmt->execute([$etudiant_id]);
    $soutenance = $stmt->fetch();
}

$noteVisible = false;
$joursRestants = 0;

if ($soutenance) {
    $dateSoutenance = new DateTime($soutenance['date_soutenance']);
    $dateAujourdhui = new DateTime();
    $datePublication = clone $dateSoutenance;
    $datePublication->modify('+7 days');

    if ($dateAujourdhui >= $datePublication) {
        $noteVisible = true;
    } else {
        $diff = $dateAujourdhui->diff($datePublication);
        $joursRestants = $diff->days;
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="fw-bold m-0" style="color: #0055A4;">Mes Notes</h4>
                </div>
                <div class="card-body p-4 text-center">
                    
                    <?php if (!$soutenance): ?>
                        <p class="text-muted">Aucune note enregistrée pour <strong><?= htmlspecialchars($etudiant_id) ?></strong>.</p>

                    <?php elseif ($noteVisible): ?>
                        <div class="row">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block">Soutenance</small>
                                <span class="display-6 fw-bold"><?= $soutenance['note_soutenance'] ?>/20</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Rapport</small>
                                <span class="display-6 fw-bold"><?= $soutenance['note_rapport'] ?>/20</span>
                            </div>
                        </div>
                        <div class="alert alert-success mt-4">Notes publiées le <?= $datePublication->format('d/m/Y') ?></div>

                    <?php else: ?>
                        <div class="spinner-border text-primary mb-3"></div>
                        <h5>Notes en attente de publication</h5>
                        <p>Disponibles dans <strong><?= $joursRestants + 1 ?> jour(s)</strong>.</p>
                        <p class="small text-muted">(Soutenance effectuée le <?= date('d/m/Y', strtotime($soutenance['date_soutenance'])) ?>)</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>