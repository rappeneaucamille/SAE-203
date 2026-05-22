<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

if ($_SESSION['role'] !== 'etudiant') { header('Location: ../../index.php'); exit(); }

// 1. Traitement de l'ajout d'une démarche (formulaire de droite)
if (isset($_POST['add_demarche'])) {
    $entreprise = htmlspecialchars($_POST['entreprise']);
    $etat = htmlspecialchars($_POST['etat']);
    // On enregistre ça dans une table de suivi (à adapter selon ta base SQL)
    // Ici, je fais une simulation simple pour l'affichage
    $msg_info = "Démarche enregistrée pour $entreprise !";
}

// 2. Récupération des offres de stage en base
$offres = $pdo->query("SELECT * FROM Offre ORDER BY id_offre DESC");
?>

<div class="container">
    <h2 class="fw-bold mb-4">Ma Recherche de Stage</h2>

    <?php if(isset($msg_info)) echo "<div class='alert alert-info'>$msg_info</div>"; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card p-4 shadow-sm">
                <h5 class="mb-4 text-primary">📋 Offres de stage du département</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Intitulé</th>
                                <th>Lieu</th>
                                <th>Rémunération</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($o = $offres->fetch()): ?>
                            <tr>
                                <td><strong><?= $o['intitule'] ?></strong></td>
                                <td><?= $o['lieu'] ?></td>
                                <td><?= $o['remuneration'] ?> €</td>
                                <td><button class="btn btn-sm btn-outline-mmi">Voir détails</button></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-4 border-top border-4 border-mmi">
                <h5 class="mb-3">Suivi de mes démarches</h5>
                <form method="POST" class="mb-4">
                    <div class="mb-2">
                        <input type="text" name="entreprise" class="form-control form-control-sm" placeholder="Nom de l'entreprise" required>
                    </div>
                    <div class="mb-2">
                        <select name="etat" class="form-select form-select-sm">
                            <option>Contacté</option>
                            <option>Entretien prévu</option>
                            <option>Refusé</option>
                            <option>Accepté</option>
                        </select>
                    </div>
                    <button type="submit" name="add_demarche" class="btn btn-mmi btn-sm w-100">Ajouter</button>
                </form>

                <hr>
                <h6>Mes contacts récents :</h6>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Ubisoft <span class="badge bg-warning">En attente</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Google <span class="badge bg-danger">Refusé</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>