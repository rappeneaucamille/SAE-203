<?php 
require_once '../../includes/db.php';
include '../../includes/header.php';

if ($_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// --- LOGIQUE DE SUPPRESSION ---
if (isset($_GET['delete_user']) && isset($_GET['type'])) {
    $id = $_GET['delete_user'];
    if ($_GET['type'] == 'prof') {
        $pdo->prepare("DELETE FROM Enseignant WHERE identifiant = ?")->execute([$id]);
    } else {
        $pdo->prepare("DELETE FROM Etudiant WHERE num_etudiant = ?")->execute([$id]);
    }
    header('Location: gestion.php?status=deleted');
    exit();
}

$profs = $pdo->query("SELECT * FROM Enseignant ORDER BY nom ASC")->fetchAll();
$etudiants = $pdo->query("SELECT * FROM Etudiant ORDER BY nom ASC")->fetchAll();
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

    <div class="mb-4">
        <input type="text" id="tableSearch" class="form-control shadow-sm" placeholder="Rechercher un nom, un email, une promo...">
    </div>

    <div class="card shadow-sm mb-5 border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Enseignants & Staff</h5>
            <a href="../../auth/inscription_enseignant.php" class="btn btn-success btn-sm">Ajouter Enseignant</a>
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
                    <?php foreach($profs as $p): ?>
                    <tr>
                        <td><strong><?= strtoupper($p['nom']) ?></strong> <?= $p['prenom'] ?><br><small><?= $p['identifiant'] ?></small></td>
                        <td><span class="badge bg-info text-dark"><?= $p['fonctions'] ?></span></td>
                        <td class="text-end">
                            <a href="edit_user.php?id=<?= $p['identifiant'] ?>&type=prof" class="btn btn-sm btn-primary">Modifier</a>
                            <a href="gestion.php?delete_user=<?= $p['identifiant'] ?>&type=prof" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce prof ?')">Supprimer</a>
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
                    <?php foreach($etudiants as $e): ?>
                    <tr>
                        <td><strong><?= strtoupper($e['nom']) ?></strong> <?= $e['prenom'] ?></td>
                        <td><?= $e['promotion'] ?></td>
                        <td class="text-end">
                            <a href="edit_user.php?id=<?= $e['num_etudiant'] ?>&type=etud" class="btn btn-sm btn-primary">Modifier</a>
                            <a href="gestion.php?delete_user=<?= $e['num_etudiant'] ?>&type=etud" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet étudiant ?')">Supprimer</a>
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