<?php 
require_once '../includes/db.php';
include '../includes/header.php';

if ($_SESSION['role'] !== 'Responsable stage') header('Location: ../index.php');

// Traitement de la planification
if (isset($_POST['planifier'])) {
    $id_stage = $_POST['id_stage'];
    $date = $_POST['date'];
    $salle = htmlspecialchars($_POST['salle']);
    $id_jury = $_POST['id_jury'];

    try {
        $sql = "INSERT INTO soutenance (date_soutenance, salle, id_jury) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$date, $salle, $id_jury]);
        $id_sout = $pdo->lastInsertId();

        // On lie la soutenance au stage
        $update = $pdo->prepare("UPDATE stage SET id_soutenance = ? WHERE id_stage = ?");
        $update->execute([$id_sout, $id_stage]);
        
        $msg = "Soutenance planifiée !";
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}
?>

<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Organisation des Oraux</h2>

    <div class="card p-4 shadow-sm mb-5">
        <h5><i class="bi bi-calendar-plus"></i> Programmer une soutenance</h5>
        <hr>
        <form method="POST" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Étudiant / Stage</label>
                <select name="id_stage" class="form-select" required>
                    <?php 
                    $stages = $pdo->query("SELECT s.id_stage, e.nom, e.prenom FROM stage s JOIN etudiant e ON s.num_etudiant = e.num_etudiant WHERE s.id_soutenance IS NULL");
                    while($s = $stages->fetch()) echo "<option value='{$s['id_stage']}'>{$s['nom']} {$s['prenom']}</option>";
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date & Heure</label>
                <input type="datetime-local" name="date" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Salle</label>
                <input type="text" name="salle" class="form-control" placeholder="Ex: A102" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Jury Référent</label>
                <select name="id_jury" class="form-select" required>
                    <?php 
                    $jurys = $pdo->query("SELECT j.id_jury, e.nom FROM jury j JOIN enseignant e ON j.id_ens = e.id_ens");
                    while($j = $jurys->fetch()) echo "<option value='{$j['id_jury']}'>Jury de M. {$j['nom']}</option>";
                    ?>
                </select>
            </div>
            <div class="col-12 text-end">
                <button type="submit" name="planifier" class="btn btn-dark px-5">Enregistrer la convocation</button>
            </div>
        </form>
    </div>

    <div class="card p-4 shadow-sm">
        <h5>Planning des soutenances</h5>
        <table class="table">
            <thead>
                <tr><th>Date</th><th>Étudiant</th><th>Salle</th><th>Jury</th></tr>
            </thead>
            <tbody>
                <?php 
                $planning = $pdo->query("SELECT so.*, et.nom as etnom, et.prenom as etpre FROM soutenance so JOIN stage st ON so.id_soutenance = st.id_soutenance JOIN etudiant et ON st.num_etudiant = et.num_etudiant ORDER BY date_soutenance ASC");
                while($p = $planning->fetch()): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($p['date_soutenance'])) ?></td>
                    <td><?= $p['etnom'] ?> <?= $p['etpre'] ?></td>
                    <td><?= $p['salle'] ?></td>
                    <td><span class="badge bg-secondary">Jury <?= $p['id_jury'] ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>