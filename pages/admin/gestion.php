<?php 
require_once '../../includes/db.php';
include '../../includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// --- LOGIQUE DE VALIDATION DU COMPTE ---
if (isset($_GET['validate_user']) && isset($_GET['type'])) {
    $id = $_GET['validate_user'];
    if ($_GET['type'] == 'prof') {
        $pdo->prepare("UPDATE Enseignant SET statut_compte = 'Validé' WHERE LOWER(identifiant) = LOWER(?)")->execute([$id]);
    } else {
        $pdo->prepare("UPDATE Etudiant SET statut_compte = 'Validé' WHERE num_etudiant = ?")->execute([$id]);
    }
    header('Location: gestion.php?status=validated');
    exit();
}

// --- LOGIQUE DE SUPPRESSION ---
if (isset($_GET['delete_user']) && isset($_GET['type'])) {
    $id = $_GET['delete_user'];
    if ($_GET['type'] == 'prof') {
        $pdo->prepare("DELETE FROM Enseignant WHERE LOWER(identifiant) = LOWER(?)")->execute([$id]);
    } else {
        $pdo->prepare("DELETE FROM stage WHERE num_etudiant = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM Etudiant WHERE num_etudiant = ?")->execute([$id]);
    }
    header('Location: gestion.php?status=deleted');
    exit();
}

$profs = $pdo->query("SELECT * FROM Enseignant ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
$etudiants = $pdo->query("SELECT * FROM Etudiant ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: #0055A4;">Administration</h2>
        <div class="btn-group shadow-sm">
            <a href="../responsable/dashboard.php" class="btn btn-primary btn-sm px-3">
                <i class="bi bi-briefcase"></i> Mode Responsable
            </a>
            <a href="../jury/notes.php" class="btn btn-secondary btn-sm px-3">
                <i class="bi bi-mortarboard"></i> Mode Jury
            </a>
        </div>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'validated'): ?>
        <div class="alert alert-success shadow-sm border-0">Le compte a été validé avec succès !</div>
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
        <div class="alert alert-danger shadow-sm border-0">Le compte a été supprimé.</div>
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'added'): ?>
        <div class="alert alert-success shadow-sm border-0">Le compte a été ajouté avec succès.</div>
    <?php endif; ?>

    <div class="mb-4">
        <input type="text" id="tableSearch" class="form-control shadow-sm" placeholder="Rechercher un nom, un email, une promo...">
    </div>

    <div class="card shadow-sm mb-5 border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Enseignants & Staff</h5>
            <a href="add_enseignant.php" class="btn btn-success btn-sm">Ajouter Enseignant</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Rôle</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="adminTableProf">
                    <?php foreach($profs as $p): 
                        $raw_statut = $p['statut_compte'] ?? $p['Statut_compte'] ?? 'Validé';
                        $statut_prof = trim(strtolower($raw_statut));
                        $email_prof = $p['identifiant'] ?? $p['Identifiant'];
                    ?>
                    <tr>
                        <td>
                            <strong><?= strtoupper($p['nom']) ?></strong> <?= $p['prenom'] ?>
                            <?php if($statut_prof === 'en attente'): ?>
                                <span class="badge bg-warning text-dark ms-2">En attente</span>
                            <?php endif; ?>
                            <br><small><?= $email_prof ?></small>
                        </td>
                        <td><span class="badge bg-info text-dark"><?= $p['fonctions'] ?></span></td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <?php if($statut_prof === 'en attente'): ?>
                                    <a href="gestion.php?validate_user=<?= urlencode($email_prof) ?>&type=prof" class="btn btn-sm btn-success">Valider le compte</a>
                                <?php endif; ?>
                                <a href="edit_user.php?id=<?= urlencode($email_prof) ?>&type=prof" class="btn btn-sm btn-primary">Modifier</a>
                                <a href="gestion.php?delete_user=<?= urlencode($email_prof) ?>&type=prof" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce prof ?')">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Étudiants</h5>
            <a href="add_etudiant.php" class="btn btn-light btn-sm">Ajouter un Étudiant</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Promo</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="adminTableEtud">
                    <?php foreach($etudiants as $e): 
                        $raw_statut_etud = $e['statut_compte'] ?? $e['Statut_compte'] ?? 'Validé';
                        $statut_etud = trim(strtolower($raw_statut_etud));
                    ?>
                    <tr>
                        <td>
                            <strong><?= strtoupper($e['nom']) ?></strong> <?= $e['prenom'] ?>
                            <?php if($statut_etud === 'en attente'): ?>
                                <span class="badge bg-warning text-dark ms-2">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $e['promotion'] ?></td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <?php if($statut_etud === 'en attente'): ?>
                                    <a href="gestion.php?validate_user=<?= $e['num_etudiant'] ?>&type=etud" class="btn btn-sm btn-success">Valider le compte</a>
                                <?php endif; ?>
                                <a href="edit_user.php?id=<?= $e['num_etudiant'] ?>&type=etud" class="btn btn-sm btn-primary">Modifier</a>
                                <a href="gestion.php?delete_user=<?= $e['num_etudiant'] ?>&type=etud" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet étudiant ?')">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let rows = document.querySelectorAll("tbody tr");
    rows.forEach(row => {
        row.style.display = row.textContent.toUpperCase().includes(filter) ? "" : "none";
    });
});
</script>
<?php include '../../includes/footer.php'; ?>