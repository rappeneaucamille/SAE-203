<?php 
require_once '../includes/db.php';
include '../includes/header.php';

if ($_SESSION['role'] !== 'Responsable stage') header('Location: ../index.php');

// Traitement de l'affectation
if (isset($_POST['affecter'])) {
    $num_etud = $_POST['num_etudiant'];
    $id_ent = $_POST['id_ent'];
    $id_maitre = $_POST['id_maitre'];
    $id_ens = $_POST['id_ens']; // Le tuteur enseignant

    try {
        $sql = "INSERT INTO stage (num_etudiant, id_ent, id_maitre, id_ens) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$num_etud, $id_ent, $id_maitre, $id_ens]);
        $msg = "Affectation réussie !";
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}
?>

<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Suivi et Affectation des Stages</h2>
    
    <?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card p-4 shadow-sm h-100">
                <h5><i class="bi bi-search"></i> État des recherches</h5>
                <hr>
                <div class="table-responsive">
                    <div class="mb-3">
                        <input type="text" id="tableSearch" class="form-control" placeholder="Rechercher un nom, une promo ou une entreprise...">
                    </div>
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr><th>Étudiant</th><th>Démarches</th><th>Statut</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $etudiants = $pdo->query("SELECT * FROM etudiant ORDER BY nom ASC");
                            while($e = $etudiants->fetch()): ?>
                            <tr>
                                <td><?= strtoupper($e['nom']) ?> <?= $e['prenom'] ?></td>
                                <td><span class="badge bg-info">3 contacts</span></td>
                                <td>
                                    <?php 
                                    // Vérifier si déjà affecté
                                    $check = $pdo->prepare("SELECT id_stage FROM stage WHERE num_etudiant = ?");
                                    $check->execute([$e['num_etudiant']]);
                                    if($check->fetch()) echo '<span class="text-success">Affecté</span>';
                                    else echo '<span class="text-warning">En recherche</span>';
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card p-4 border-primary shadow-sm">
                <h5><i class="bi bi-person-check"></i> Créer une affectation</h5>
                <hr>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small">Étudiant</label>
                        <select name="num_etudiant" class="form-select" required>
                            <?php 
                            $etuds = $pdo->query("SELECT num_etudiant, nom, prenom FROM etudiant");
                            while($et = $etuds->fetch()) echo "<option value='{$et['num_etudiant']}'>{$et['nom']} {$et['prenom']}</option>";
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Entreprise</label>
                        <select name="id_ent" class="form-select" required>
                            <?php 
                            $ents = $pdo->query("SELECT id_ent, nom_ent FROM entreprise");
                            while($en = $ents->fetch()) echo "<option value='{$en['id_ent']}'>{$en['nom_ent']}</option>";
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Tuteur Enseignant</label>
                        <select name="id_ens" class="form-select" required>
                            <?php 
                            $profs = $pdo->query("SELECT id_ens, nom, prenom FROM enseignant");
                            while($p = $profs->fetch()) echo "<option value='{$p['id_ens']}'>{$p['nom']} {$p['prenom']}</option>";
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="affecter" class="btn btn-primary w-100">Valider l'affectation</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>