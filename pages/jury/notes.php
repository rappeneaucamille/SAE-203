<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité
if ($_SESSION['role'] !== 'Jury de soutenance' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

$mon_email = $_SESSION['identifiant'] ?? '';

// Correction de la requête SQL (Nettoyage de la clause WHERE)
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

<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color: #2e4588;">Saisie des Notes de Soutenance</h2>
    <p class="text-muted mb-4"><i class="bi bi-info-circle"></i> Affichage des soutenances dont vous êtes membre du jury.</p>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success">Note enregistrée avec succès !</div>
    <?php endif; ?>

    <div class="card p-3 mb-4 shadow-sm border-0 bg-light">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="tableSearch" class="form-control border-start-0" placeholder="Rechercher un étudiant ou une promo...">
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Détails Soutenance & Étudiant</th>
                        <th>Promotion</th>
                        <th style="width: 150px;">Note Rapport</th>
                        <th style="width: 150px;">Note Oral</th>
                        <th>Moyenne</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="notesTable">
                    <?php if (empty($soutenances)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Vous n'avez aucune soutenance programmée pour le moment.</td></tr>
                    <?php else: ?>
                        <?php foreach($soutenances as $s): 
                            $moyenne = ($s['note_rapport'] + $s['note_soutenance']) / 2;
                            $badge_color = ($moyenne >= 10) ? 'bg-success' : 'bg-danger';
                        ?>
                        <tr>
                            <form action="save_notes.php" method="POST">
                                <input type="hidden" name="id_soutenance" value="<?= $s['id_soutenance'] ?>">
                                
                                <td>
                                    <div class="mb-1">
                                        <span class="badge bg-primary"><?= date('d/m/Y', strtotime($s['date_soutenance'])) ?></span>
                                        <span class="badge bg-dark"><?= substr($s['heure_debut'], 0, 5) ?> - <?= substr($s['heure_fin'], 0, 5) ?></span>
                                        <span class="badge bg-secondary">Salle : <?= htmlspecialchars($s['salle']) ?></span>
                                    </div>
                                    <strong><?= strtoupper($s['nom'] ?? 'Inconnu') ?></strong> <?= $s['prenom'] ?? '' ?>
                                </td>
                                <td><span class="badge bg-secondary"><?= $s['promotion'] ?? 'N/A' ?></span></td>
                                
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.25" min="0" max="20" name="note_rapport" 
                                               class="form-control" value="<?= $s['note_rapport'] ?>">
                                        <span class="input-group-text">/20</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.25" min="0" max="20" name="note_soutenance" 
                                               class="form-control" value="<?= $s['note_soutenance'] ?>">
                                        <span class="input-group-text">/20</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= $badge_color ?> px-3 py-2">
                                        <?= number_format($moyenne, 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-primary btn-sm fw-bold">
                                        <i class="bi bi-check-lg"></i> ENREGISTRER
                                    </button>
                                </td>
                            </form>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let rows = document.querySelector("#notesTable").rows;
    for (let i = 0; i < rows.length; i++) {
        let text = rows[i].textContent.toUpperCase();
        rows[i].style.display = text.includes(filter) ? "" : "none";
    }
});
</script>

<?php include '../../includes/footer.php'; ?>