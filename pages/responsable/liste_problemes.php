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
    exit();
}

// On récupère les stages qui ont une alerte
$sql = "SELECT s.*, e.nom, e.prenom, e.identifiant as email 
        FROM Stage s 
        JOIN Etudiant e ON s.num_etudiant = e.num_etudiant 
        WHERE s.alerte_etudiant IS NOT NULL AND s.alerte_etudiant != ''";
$incidents = $pdo->query($sql)->fetchAll();
?>

<link rel="stylesheet" href="../../assets/css/style.css">

<div class="container py-5" style="max-width: 1140px;">
    <h1 class="fw-bold mb-5 d-flex align-items-center gap-3" style="color: #D93838; font-size: 2.2rem; letter-spacing: -0.5px;">
        <i class="bi bi-exclamation-triangle"></i> Alertes Étudiants
    </h1>

    <?php if(empty($incidents)): ?>
        <div class="bg-white p-5 text-center border-0" style="border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.05);">
            <i class="bi bi-check2-circle display-4 text-success mb-3 d-block"></i>
            <h5 class="fw-bold text-dark mb-1">Aucune alerte</h5>
            <p class="text-muted mb-0 small">Tous les incidents signalés ont été résolus.</p>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-4">
            <?php foreach($incidents as $i): ?>
                
                <div class="bg-white p-4 border-0 d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap gap-4" 
                     style="border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.05), 0 3px 10px rgba(0,0,0,0.015) !important;">
                    
                    <div style="min-width: 220px; flex: 1;">
                        <h4 class="fw-bold mb-2" style="color: #000000; font-size: 1.3rem; letter-spacing: -0.3px;">
                            <?= htmlspecialchars($i['prenom']) ?> <?= strtoupper($i['nom']) ?>
                        </h4>
                        <span class="text-secondary small d-block" style="font-size: 0.95rem;">
                            Stage chez : <strong class="text-dark fw-semibold"><?= htmlspecialchars($i['lieu']) ?></strong>
                        </span>
                    </div>

                    <div style="min-width: 320px; flex: 2.5;">
                        <div class="p-3 border-0 text-secondary" style="background-color: #F8FAFC; border-radius: 12px; font-size: 0.95rem; font-style: italic; line-height: 1.5; color: #334155 !important;">
                            "<?= htmlspecialchars($i['alerte_etudiant']) ?>"
                        </div>
                    </div>

                    <div class="text-end" style="min-width: 180px;">
                        <a href="liste_problemes.php?action=clear&id=<?= $i['id_stage'] ?>" 
                           class="btn btn-outline-success fw-medium w-100 py-2 d-inline-flex align-items-center justify-content-center gap-2" 
                           style="border-color: #76BA99; color: #4E9F75; border-radius: 8px; font-size: 0.85rem; height: 43px; letter-spacing: 0.2px; background-color: transparent;">
                            Marqué comme réglé
                        </a>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>