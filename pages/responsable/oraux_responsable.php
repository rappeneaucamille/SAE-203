<?php 
require_once '../../includes/db.php';
include '../../includes/header.php';

if ($_SESSION['role'] !== 'Responsable stage') header('Location: ../../index.php');

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

        $update = $pdo->prepare("UPDATE stage SET id_soutenance = ? WHERE id_stage = ?");
        $update->execute([$id_sout, $id_stage]);
        
        echo "<div class='alert alert-success'>Soutenance planifiée avec succès !</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
    }
}
?>

<div class="container py-4">
    <h2 class="mb-4 fw-bold"><i class="bi bi-calendar-event"></i> Organisation des Oraux</h2>

    <div class="card p-4 shadow-sm mb-5 border-0">
        <h5 class="fw-bold mb-3">Planifier un passage</h5>
        <form method="POST" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Étudiant (ayant un stage validé)</label>
                <select name="id_stage" class="form-select" required>
                    <option value="">-- Sélectionner l'étudiant --</option>
                    <?php 
                    // On cherche les étudiants qui ont une ligne dans la table Stage
                    $etudiants = $pdo->query("SELECT s.id_stage, e.nom, e.prenom FROM stage s JOIN etudiant e ON s.num_etudiant = e.num_etudiant");
                    while($et = $etudiants->fetch()) {
                        echo "<option value='{$et['id_stage']}'>{$et['nom']} {$et['prenom']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Date et Heure</label>
                <input type="datetime-local" name="date" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Salle</label>
                <input type="text" name="salle" class="form-control" placeholder="ex: B102">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Jury</label>
                <select name="id_jury" class="form-select">
                    <?php 
                    // On récupère les jurys et le nom de l'enseignant associé
                    $jurys = $pdo->query("SELECT j.id_jury, e.nom FROM jury j JOIN enseignant e ON j.id_ens = e.id_ens");
                    while($j = $jurys->fetch()) {
                        echo "<option value='{$j['id_jury']}'>Jury n°{$j['id_jury']} (M. {$j['nom']})</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-12 mt-3">
                <button type="submit" name="planifier" class="btn btn-primary fw-bold px-4">ENREGISTRER LA CONVOCATION</button>
            </div>
        </form>
    </div>

    <div class="card p-4 shadow-sm border-0">
        <h5 class="fw-bold mb-3">Planning des soutenances</h5>
        <table class="table align-middle">
            <thead class="table-light">
                <tr><th>Date</th><th>Étudiant</th><th>Salle</th><th>Jury</th></tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT so.*, et.nom, et.prenom 
                        FROM soutenance so 
                        JOIN stage st ON so.id_soutenance = st.id_soutenance 
                        JOIN etudiant et ON st.num_etudiant = et.num_etudiant 
                        ORDER BY date_soutenance ASC";
                $res = $pdo->query($sql)->fetchAll();
                foreach($res as $p): ?>
                <tr>
                    <td><?= date('d/m/Y à H:i', strtotime($p['date_soutenance'])) ?></td>
                    <td><strong><?= strtoupper($p['nom']) ?></strong> <?= $p['prenom'] ?></td>
                    <td><span class="badge bg-light text-dark border"><?= $p['salle'] ?></span></td>
                    <td>Jury <?= $p['id_jury'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>