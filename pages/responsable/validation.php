<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Action de validation/refus
if (isset($_GET['action']) && isset($_GET['id'])) {
    $nouveau_statut = ($_GET['action'] == 'valider') ? 'Validée' : 'Refusé';
    $stmt = $pdo->prepare("UPDATE Recherche SET statut = ? WHERE id_recherche = ?");
    $stmt->execute([$nouveau_statut, $_GET['id']]);
    echo "<div class='alert alert-success'>Statut mis à jour !</div>";
}

$recherches = $pdo->query("SELECT r.*, e.nom, e.prenom FROM Recherche r JOIN Effectuer ef ON r.id_recherche = ef.id_recherche JOIN Etudiant e ON ef.num_etudiant = e.num_etudiant")->fetchAll();
?>

<div class="container">
    <h2 class="fw-bold mb-4">Validation des dossiers</h2>
    <table class="table card p-3 shadow-sm">
        <thead>
            <tr><th>Étudiant</th><th>Entreprise</th><th>Statut</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach($recherches as $r): ?>
            <tr>
                <td><?= $r['nom'] ?></td>
                <td><?= $r['entreprise_contactee'] ?></td>
                <td><span class="badge bg-secondary"><?= $r['statut'] ?></span></td>
                <td>
                    <a href="validation.php?action=valider&id=<?= $r['id_recherche'] ?>" class="btn btn-success btn-sm">Valider</a>
                    <a href="validation.php?action=refuser&id=<?= $r['id_recherche'] ?>" class="btn btn-danger btn-sm">Refuser</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../../includes/footer.php'; ?>