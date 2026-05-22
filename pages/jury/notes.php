<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

if ($_SESSION['role'] !== 'Jury de soutenance') { header('Location: ../../index.php'); exit(); }

// Traitement de la saisie
if (isset($_POST['save_note'])) {
    $id_soutenance = $_POST['id_soutenance'];
    $note_r = $_POST['note_rapport'];
    $note_s = $_POST['note_soutenance'];
    $obs = htmlspecialchars($_POST['observation']);

    $stmt = $pdo->prepare("UPDATE Soutenance SET note_rapport = ?, note_soutenance = ?, observation_jury = ? WHERE id_soutenance = ?");
    $stmt->execute([$note_r, $note_s, $obs, $id_soutenance]);
    $success = "Notes enregistrées avec succès !";
}

// On récupère les soutenances assignées à ce jury
$soutenances = $pdo->query("
    SELECT s.*, e.nom, e.prenom 
    FROM Soutenance s
    JOIN Stage st ON s.id_soutenance = st.id_soutenance
    JOIN Etudiant e ON st.num_etudiant = e.num_etudiant
")->fetchAll();
?>

<div class="container">
    <h2 class="fw-bold mb-4">Saisie des Notes de Soutenance</h2>
    
    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

    <div class="table-responsive">
        <div class="mb-3">
            <input type="text" id="tableSearch" class="form-control" placeholder="Rechercher un nom, une promo ou une entreprise...">
        </div>
        <table class="table table-bordered bg-white shadow-sm align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Étudiant</th>
                    <th>Rapport (/20)</th>
                    <th>Oral (/20)</th>
                    <th>Observations</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($soutenances as $row): ?>
                <form method="POST">
                    <tr>
                        <td><strong><?= $row['nom'] ?> <?= $row['prenom'] ?></strong></td>
                        <td style="width: 100px;"><input type="number" step="0.5" name="note_rapport" class="form-control" value="<?= $row['note_rapport'] ?>"></td>
                        <td style="width: 100px;"><input type="number" step="0.5" name="note_soutenance" class="form-control" value="<?= $row['note_soutenance'] ?>"></td>
                        <td><input type="text" name="observation" class="form-control" value="<?= $row['observation_jury'] ?>"></td>
                        <td>
                            <input type="hidden" name="id_soutenance" value="<?= $row['id_soutenance'] ?>">
                            <button type="submit" name="save_note" class="btn btn-primary btn-sm">Enregistrer</button>
                        </td>
                    </tr>
                </form>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>