<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité : Seul le jury ou l'admin peut entrer
if ($_SESSION['role'] !== 'Jury de soutenance' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// Récupération des soutenances avec les noms des étudiants
$sql = "SELECT s.*, e.nom, e.prenom, e.promotion 
        FROM Soutenance s
        LEFT JOIN Etudiant e ON s.etudiant = e.identifiant 
        ORDER BY s.date_soutenance ASC";
$soutenances = $pdo->query($sql)->fetchAll();
?>

<div class="container">
    <h2 class="fw-bold mb-4" style="color: #0055A4;">Saisie des Notes de Soutenance</h2>

    <div class="card p-3 mb-4 shadow-sm border-0 bg-light">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="tableSearch" class="form-control border-start-0" placeholder="Rechercher un étudiant ou une promo...">
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Étudiant</th>
                        <th>Promotion</th>
                        <th>Note Rapport /20</th>
                        <th>Note Soutenance /20</th>
                        <th class="bg-primary text-white">MOYENNE</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="notesTable">
                    <?php foreach($soutenances as $s): 
                        // Calcul de la moyenne
                        $moyenne = ($s['note_rapport'] + $s['note_soutenance']) / 2;
                        $badge_color = ($moyenne >= 10) ? 'bg-success' : 'bg-danger';
                    ?>
                    <tr>
                        <td><strong><?= strtoupper($s['nom']) ?></strong> <?= $s['prenom'] ?></td>
                        <td><span class="badge bg-secondary"><?= $s['promotion'] ?></span></td>
                        
                        <form action="save_notes.php" method="POST">
                            <input type="hidden" name="id_soutenance" value="<?= $s['id_soutenance'] ?>">
                            <td>
                                <input type="number" step="0.25" min="0" max="20" name="note_rapport" 
                                       class="form-control form-control-sm w-75" value="<?= $s['note_rapport'] ?>">
                            </td>
                            <td>
                                <input type="number" step="0.25" min="0" max="20" name="note_soutenance" 
                                       class="form-control form-control-sm w-75" value="<?= $s['note_soutenance'] ?>">
                            </td>
                            <td>
                                <span class="badge <?= $badge_color ?> fs-6">
                                    <?= number_format($moyenne, 2) ?> / 20
                                </span>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-sm btn-dark">Enregistrer</button>
                            </td>
                        </form>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>