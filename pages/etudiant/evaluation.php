<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'etudiant') {
    header('Location: ../../index.php');
    exit();
}

// On récupère l'identifiant
$etudiant_id = $_SESSION['identifiant'] ?? ''; 

$soutenance = false;
if (!empty($etudiant_id)) {
    // AJOUT : On récupère aussi la salle, les horaires et le jury affecté
    $stmt = $pdo->prepare("SELECT s.*, j.enseignant_1, j.enseignant_2 
                           FROM soutenance s 
                           LEFT JOIN jury j ON s.id_jury = j.id_jury 
                           WHERE LOWER(s.etudiant) = LOWER(?)");
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
    <div class="row justify-content-center g-4">
        
        <?php if ($soutenance): ?>
        <div class="col-md-8">
            <div class="card shadow border-0 text-white" style="background-color: #0055A4;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-calendar-check"></i> Ma Convocation aux Soutenances</h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="opacity-75 d-block">Date et Heure</small>
                            <span class="fw-bold fs-5"><?= date('d/m/Y', strtotime($soutenance['date_soutenance'])) ?></span>
                            <span class="d-block small opacity-75">De <?= substr($soutenance['heure_debut'], 0, 5) ?> à <?= substr($soutenance['heure_fin'], 0, 5) ?></span>
                        </div>
                        <div class="col-sm-6">
                            <small class="opacity-75 d-block">LSalle</small>
                            <span class="fw-bold fs-5"><span class="badge bg-white text-primary"><?= htmlspecialchars($soutenance['salle']) ?></span></span>
                        </div>
                        <div class="col-11">
                            <small class="opacity-75 d-block">Composition de votre Jury</small>
                            <ul class="mb-0 small ps-3">
                                <li><?= htmlspecialchars($soutenance['enseignant_1'] ?? 'Non assigné') ?></li>
                                <li><?= htmlspecialchars($soutenance['enseignant_2'] ?? 'Non assigné') ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="fw-bold m-0" style="color: #0055A4;">Mes Notes</h4>
                </div>
                <div class="card-body p-4 text-center">
                    
                    <?php if (!$soutenance): ?>
                        <p class="text-muted mb-0">Aucune soutenance programmée ni note enregistrée pour <strong><?= htmlspecialchars($etudiant_id) ?></strong>.</p>

                    <?php elseif ($noteVisible): ?>
                        <div class="row">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block">Soutenance (Oral)</small>
                                <span class="display-6 fw-bold text-success"><?= $soutenance['note_soutenance'] ?? 'N/A' ?>/20</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Rapport Écrit</small>
                                <span class="display-6 fw-bold text-success"><?= $soutenance['note_rapport'] ?? 'N/A' ?>/20</span>
                            </div>
                        </div>
                        <div class="alert alert-success mt-4 mb-0">Notes publiées le <?= $datePublication->format('d/m/Y') ?></div>

                    <?php else: ?>
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <h5>Notes en attente de publication</h5>
                        <p class="mb-1">Disponibles dans <strong><?= $joursRestants + 1 ?> jour(s)</strong>.</p>
                        <p class="small text-muted mb-0">(Les notes de la soutenance du <?= date('d/m/Y', strtotime($soutenance['date_soutenance'])) ?> restent secrètes pendant 7 jours)</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        
    </div>
</div>

<?php include '../../includes/footer.php'; ?>