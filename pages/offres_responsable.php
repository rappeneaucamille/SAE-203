<?php 
require_once '../includes/db.php';
include '../includes/header.php';

// 1. TRAITEMENT DU FORMULAIRE (Tout en haut du fichier)
if (isset($_POST['add_offre'])) {
    try {
        $sql = "INSERT INTO Offre (intitule, contact, lieu, description, remuneration, competences) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['titre'], 
            $_POST['contact'], 
            $_POST['lieu'], 
            $_POST['desc'], 
            $_POST['remun'], 
            $_POST['comp']
        ]);
        $msg_success = "L'offre a été publiée avec succès !";
    } catch (PDOException $e) {
        $msg_error = "Erreur lors de la publication : " . $e->getMessage();
    }
}
?>

<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Gestion des Offres</h2>

    <?php if(isset($msg_success)) echo "<div class='alert alert-success'>$msg_success</div>"; ?>
    <?php if(isset($msg_error)) echo "<div class='alert alert-danger'>$msg_error</div>"; ?>

    <div class="card p-4 mb-5 shadow-sm">
        <h5>Saisir une nouvelle offre</h5><hr>
        <form action="" method="POST">
            <div class="row g-3">
                <div class="col-md-6"><label>Intitulé</label><input type="text" name="titre" class="form-control" required></div>
                <div class="col-md-6"><label>Entreprise / Contact</label><input type="text" name="contact" class="form-control" required></div>
                <div class="col-md-4"><label>Lieu</label><input type="text" name="lieu" class="form-control"></div>
                <div class="col-md-4"><label>Rémunération (€)</label><input type="number" name="remun" class="form-control"></div>
                <div class="col-md-4"><label>Compétences</label><input type="text" name="comp" class="form-control" placeholder="ex: HTML, CSS"></div>
                <div class="col-12"><label>Description</label><textarea name="desc" class="form-control"></textarea></div>
                <div class="col-12">
                    <button type="submit" name="add_offre" class="btn btn-primary px-4">Publier l'offre</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card p-4">
        <h5>Offres en ligne</h5>
        <table class="table">
            <thead><tr><th>Titre</th><th>Entreprise</th><th>Lieu</th></tr></thead>
            <tbody>
                <?php 
                $res = $pdo->query("SELECT * FROM Offre ORDER BY id_offre DESC");
                while($o = $res->fetch()): ?>
                <tr>
                    <td><?= $o['intitule'] ?></td>
                    <td><?= $o['contact'] ?></td>
                    <td><?= $o['lieu'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
if (file_exists('../includes/footer.php')) { include '../includes/footer.php'; } 
else { echo "</body></html>"; }
?>