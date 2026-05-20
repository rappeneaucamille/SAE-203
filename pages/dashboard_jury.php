<?php 
require_once '../includes/db.php';
include '../includes/header.php';
if ($_SESSION['role'] !== 'Jury de soutenance') header('Location: ../index.php');

// Traitement de la note
if (isset($_POST['valider_note'])) {
    $id_soutenance = $_POST['id_soutenance'];
    $n_sout = $_POST['n_soutenance'];
    $n_rapp = $_POST['n_rapport'];
    $stmt = $pdo->prepare("UPDATE Soutenance SET note_soutenance = ?, note_rapport = ? WHERE id_soutenance = ?");
    $stmt->execute([$n_sout, $n_rapp, $id_soutenance]);
    echo "<div class='alert alert-success container'>Notes enregistrées !</div>";
}
?>

<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Espace Jury de Soutenance</h2>
    <div class="card p-4">
        <h5>Saisir les évaluations</h5>
        <table class="table table-hover">
            <thead><tr><th>Étudiant</th><th>Soutenance (/20)</th><th>Rapport (/20)</th><th>Action</th></tr></thead>
            <tbody>
                <?php 
                $sql = "SELECT s.* FROM Soutenance s WHERE s.id_jury = ?";
                $stmt = $pdo->prepare($sql); $stmt->execute([$_SESSION['user_id']]);
                while($row = $stmt->fetch()): ?>
                <form method="POST">
                    <tr>
                        <td><?= $row['etudiant'] ?></td>
                        <td><input type="number" step="0.25" name="n_soutenance" class="form-control" value="<?= $row['note_soutenance'] ?>"></td>
                        <td><input type="number" step="0.25" name="n_rapport" class="form-control" value="<?= $row['note_rapport'] ?>"></td>
                        <input type="hidden" name="id_soutenance" value="<?= $row['id_soutenance'] ?>">
                        <td><button name="valider_note" class="btn btn-sm btn-primary">Enregistrer</button></td>
                    </tr>
                </form>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>