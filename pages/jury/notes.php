<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité
if ($_SESSION['role'] !== 'Jury de soutenance' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

$mon_email = $_SESSION['identifiant'] ?? '';

// Requête SQL
$sql = "SELECT s.*, e.nom, e.prenom, e.promotion, j.enseignant_1, j.enseignant_2 
        FROM soutenance s
        LEFT JOIN etudiant e ON LOWER(s.etudiant) = LOWER(e.identifiant) 
        INNER JOIN jury j ON s.id_jury = j.id_jury
        WHERE LOWER(j.enseignant_1) = LOWER(?) OR LOWER(j.enseignant_2) = LOWER(?)
        ORDER BY s.date_soutenance ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$mon_email, $mon_email]);
$soutenances = $stmt->fetchAll();
?>

<link rel="stylesheet" href="../../assets/css/style.css">
<div class="container py-5" style="max-width: 1140px;">
    <h1 class="fw-bold mb-4 text-start" style="color: #2E4588; font-size: 2.2rem; letter-spacing: -0.5px;">Saisie des Notes de Soutenance</h1>
    
    <div class="card p-2 mb-5 border-0 bg-light" style="border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <div class="input-group align-items-center px-2">
            <span class="text-muted bg-transparent border-0 me-2"><i class="bi bi-search" style="font-size: 1.1rem;"></i></span>
            <input type="text" id="tableSearch" class="form-control bg-transparent border-0 shadow-none ps-1" placeholder="Rechercher un étudiant ou une promo..." style="font-size: 0.95rem;">
        </div>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3">✨ Note enregistrée avec succès !</div>
    <?php endif; ?>

    <div id="notesContainer">
        <?php if (empty($soutenances)): ?>
            <div class="card border-0 p-5 text-center text-muted" style="border-radius: 20px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);">
                <i class="bi bi-calendar-x mb-3 text-secondary" style="font-size: 3rem Pap;"></i>
                <p class="m-0">Vous n'avez aucune soutenance programmée pour le moment.</p>
            </div>
        <?php else: ?>
            <?php foreach($soutenances as $s): 
                $moyenne = ($s['note_rapport'] + $s['note_soutenance']) / 2;
            ?>
                <div class="search-item bg-white p-4 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3" 
                     style="border-radius: 20px; border: none; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.07), 0 5px 15px rgba(0, 0, 0, 0.04);">
                    
                    <div class="d-flex align-items-center gap-4">
                        <h3 class="fw-bold m-0 text-dark" style="font-size: 1.4rem; letter-spacing: -0.3px; min-width: 180px;">
                            <?= htmlspecialchars($s['prenom'] ?? '') ?> <?= strtoupper(htmlspecialchars($s['nom'] ?? 'Inconnu')) ?>
                        </h3>
                        <div class="text-white fw-bold text-center d-flex align-items-center justify-content-center" 
                             style="background-color: #6C757D; width: 55px; height: 32px; border-radius: 6px; font-size: 0.85rem; letter-spacing: 0.5px;">
                            <?= htmlspecialchars($s['promotion'] ?? 'N/A') ?>
                        </div>
                    </div>

                    <form action="save_notes.php" method="POST" class="d-flex align-items-center gap-4 flex-grow-1 justify-content-end flex-wrap m-0">
                        <input type="hidden" name="id_soutenance" value="<?= $s['id_soutenance'] ?>">

                        <div class="text-center">
                            <span class="small fw-bold text-dark d-block mb-1" style="font-size: 0.85rem;">Note Rapport</span>
                            <div class="d-flex align-items-center gap-1 bg-light px-3 py-1" style="border-radius: 6px;">
                                <input type="number" step="0.25" min="0" max="20" name="note_rapport" 
                                       class="form-control text-center bg-transparent border-0 p-0 shadow-none fw-bold text-secondary" 
                                       style="width: 45px; font-size: 0.95rem;" 
                                       value="<?= $s['note_rapport'] ?>">
                                <span class="text-muted small">/20</span>
                            </div>
                        </div>

                        <div class="text-center">
                            <span class="small fw-bold text-dark d-block mb-1" style="font-size: 0.85rem;">Note Soutenance</span>
                            <div class="d-flex align-items-center gap-1 bg-light px-3 py-1" style="border-radius: 6px;">
                                <input type="number" step="0.25" min="0" max="20" name="note_soutenance" 
                                       class="form-control text-center bg-transparent border-0 p-0 shadow-none fw-bold text-secondary" 
                                       style="width: 45px; font-size: 0.95rem;" 
                                       value="<?= $s['note_soutenance'] ?>">
                                <span class="text-muted small">/20</span>
                            </div>
                        </div>

                        <div class="text-center px-1">
                            <span class="small fw-bold text-dark d-block mb-1" style="font-size: 0.85rem;">Moyenne</span>
                            <div class="text-white fw-bold d-flex align-items-center justify-content-center" 
                                 style="background-color: #198754; min-width: 65px; height: 32px; border-radius: 6px; font-size: 1rem;">
                                <?= number_format($moyenne, 2) ?>
                            </div>
                        </div>

                        <div class="ms-2">
                            <button type="submit" class="btn btn-primary py-2 px-4 fw-bold text-uppercase d-flex align-items-center gap-2" 
                                    style="background-color: #0066FF !important; border: none; border-radius: 8px; font-size: 0.8rem; letter-spacing: 0.5px; height: 36px;">
                                <i class="bi bi-check-circle-fill"></i> Enregistrer
                            </button>
                        </div>
                    </form>

                    <div class="w-100 mt-3 pt-2 border-top border-light-subtle text-muted d-flex gap-4" style="font-size: 0.8rem;">
                        <span><i class="bi bi-calendar3 me-1"></i> <?= date('d/m/Y', strtotime($s['date_soutenance'])) ?></span>
                        <span><i class="bi bi-clock me-1"></i> <?= substr($s['heure_debut'], 0, 5) ?> - <?= substr($s['heure_fin'], 0, 5) ?></span>
                        <span><i class="bi bi-geo-alt me-1"></i> Salle : <?= htmlspecialchars($s['salle']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
// Moteur de recherche instantané
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let items = document.querySelectorAll(".search-item");
    
    items.forEach(function(item) {
        let text = item.textContent.toUpperCase();
        item.style.setProperty('display', text.includes(filter) ? 'flex' : 'none', 'important');
    });
});
</script>

<?php include '../../includes/footer.php'; ?>