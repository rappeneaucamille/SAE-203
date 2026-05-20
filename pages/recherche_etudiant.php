<?php 
require_once '../includes/db.php';
include '../includes/header.php';
?>
<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Ma Recherche de Stage</h2>
    <div class="row">
        <div class="col-lg-7">
            <div class="card p-4 mb-4">
                <h5>Offres de stage disponibles</h5><hr>
                <div class="list-group">
                    <?php 
                    $offres = $pdo->query("SELECT * FROM Offre LIMIT 3");
                    while($o = $offres->fetch()): ?>
                        <div class="list-group-item">
                            <h6><?= $o['intitule'] ?></h6>
                            <small><?= $o['lieu'] ?> | <?= $o['contact'] ?></small>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card p-4">
                <h5>Suivi de mes démarches</h5><hr>
                <form method="POST" class="mb-3">
                    <input type="text" name="ent" class="form-control mb-2" placeholder="Entreprise contactée">
                    <button class="btn btn-success btn-sm w-100">Ajouter une candidature</button>
                </form>
                <ul class="list-group small">
                    <li class="list-group-item d-flex justify-content-between">Ubisoft <span class="badge bg-warning">En attente</span></li>
                    <li class="list-group-item d-flex justify-content-between">Google <span class="badge bg-danger">Refusé</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>