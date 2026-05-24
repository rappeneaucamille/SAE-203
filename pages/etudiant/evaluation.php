<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

$id_etud = $_SESSION['user_id'];

// On récupère la soutenance liée à l'étudiant
$sql = "SELECT * FROM Soutenance WHERE etudiant = (SELECT identifiant FROM Etudiant WHERE num_etudiant = ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_etud]);
$note = $stmt->fetch();
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4" style="color: var(--mmi-blue);">Mes Résultats de Stage</h2>

    <?php if($note): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius: 15px;">
                    <h5 class="text-muted mb-3">Note de Rapport</h5>
                    <h1 class="display-4 fw-bold text-primary"><?= $note['note_rapport'] ?> <small class="fs-4 text-muted">/ 20</small></h1>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius: 15px;">
                    <h5 class="text-muted mb-3">Note de Soutenance</h5>
                    <h1 class="display-4 fw-bold text-primary"><?= $note['note_soutenance'] ?> <small class="fs-4 text-muted">/ 20</small></h1>
                </div>
            </div>
            <div class="col-12">
                <div class="card card-pastel-blue p-4 text-center">
                    <h4 class="mb-0">Moyenne Générale : <strong><?= number_format(($note['note_rapport'] + $note['note_soutenance'])/2, 2) ?> / 20</strong></h4>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info shadow-sm">
            <i class="bi bi-clock-history"></i> Vos notes ne sont pas encore publiées. Elles le seront au plus tard une semaine après votre soutenance.
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>