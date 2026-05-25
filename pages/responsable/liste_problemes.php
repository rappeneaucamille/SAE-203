<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité
if ($_SESSION['role'] !== 'Responsable stage' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php'); exit();
}

// Action : Effacer l'alerte (si réglé)
if (isset($_GET['action']) && $_GET['action'] == 'clear' && isset($_GET['id'])) {
    $pdo->prepare("UPDATE Stage SET alerte_etudiant = NULL WHERE id_stage = ?")->execute([$_GET['id']]);
    header('Location: liste_problemes.php');
}

// On récupère les stages qui ont une alerte
$sql = "SELECT s.*, e.nom, e.prenom, e.identifiant as email 
        FROM Stage s 
        JOIN Etudiant e ON s.num_etudiant = e.num_etudiant 
        WHERE s.alerte_etudiant IS NOT NULL AND s.alerte_etudiant != ''";
$incidents = $pdo->query($sql)->fetchAll();
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4 text-danger"><i class="bi bi-exclamation-triangle"></i> Alertes Étudiants</h2>

    <?php if(empty($incidents)): ?>
        <div class="alert alert-success">✅ Aucun problème signalé actuellement.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach($incidents as $i): ?>
                <div class="col-12 mb-3">
                    <div class="card shadow-sm border-0" style="border-left: 5px solid #dc3545;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1"><?= htmlspecialchars($i['prenom']) ?> <?= strtoupper($i['nom']) ?></h5>
                                <p class="text-muted small mb-2">Stage chez : <strong><?= htmlspecialchars($i['lieu']) ?></strong></p>
                                <div class="p-2 bg-light rounded border mb-2" style="font-style: italic;">
                                    "<?= htmlspecialchars($i['alerte_etudiant']) ?>"
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="liste_problemes.php?action=clear&id=<?= $i['id_stage'] ?>" class="btn btn-outline-success btn-sm d-block">Marquer comme réglé</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>