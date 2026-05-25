<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité
if ($_SESSION['role'] !== 'Jury de soutenance' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// On récupère les soutenances
// ATTENTION : vérifie que 's.etudiant' correspond bien à 'e.identifiant' dans ta BDD
$sql = "SELECT s.*, e.nom, e.prenom, e.promotion 
        FROM Soutenance s
        LEFT JOIN Etudiant e ON s.etudiant = e.identifiant 
        ORDER BY s.date_soutenance ASC";
$soutenances = $pdo->query($sql)->fetchAll();
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color: #2e4588;">Saisie des Notes de Soutenance</h2>

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
                        <th>Étudiant</th>
                        <th>Promotion</th>
                        <th style="width: 150px;">Note Rapport</th>
                        <th style="width: 150px;">Note Oral</th>
                        <th>Moyenne</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="notesTable">
                    <?php foreach($soutenances as $s): 
                        $moyenne = ($s['note_rapport'] + $s['note_soutenance']) / 2;
                        $badge_color = ($moyenne >= 10) ? 'bg-success' : 'bg-danger';
                    ?>
                    <tr>
                        <form action="save_notes.php" method="POST">
                            <input type="hidden" name="id_soutenance" value="<?= $s['id_soutenance'] ?>">
                            
                            <td><strong><?= strtoupper($s['nom'] ?? 'Inconnu') ?></strong> <?= $s['prenom'] ?? '' ?></td>
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
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Le moteur de recherche fonctionnel
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