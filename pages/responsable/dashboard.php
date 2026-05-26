<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : On autorise le Responsable OU l'Admin
if ($_SESSION['role'] !== 'Responsable stage' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// Stats rapides
$totalEtudiants = $pdo->query("SELECT COUNT(*) FROM Etudiant")->fetchColumn();
$stagesValides = $pdo->query("SELECT COUNT(*) FROM Stage")->fetchColumn();
$enRecherche = $totalEtudiants - $stagesValides;
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Tableau de Bord Responsable</h2>
        <span class="badge bg-danger">Session Responsable</span>.    </div>

    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card text-center p-3 border-0 shadow-sm bg-primary text-white">
                <h6>Total Étudiants</h6>
                <h2 class="fw-bold"><?= $totalEtudiants ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-3 border-0 shadow-sm bg-success text-white">
                <h6>Stages Validés</h6>
                <h2 class="fw-bold"><?= $stagesValides ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-3 border-0 shadow-sm bg-warning text-dark">
                <h6>En recherche</h6>
                <h2 class="fw-bold"><?= $enRecherche ?></h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card p-4 shadow-sm">
                <h5 class="mb-4">⏳ Demandes de validation en attente</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Étudiant</th>
                                <th>Entreprise</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <div class="col-md-8">

                    <tbody>
                        <?php
                        $sql = "SELECT r.*, e.nom, e.prenom 
                                FROM Recherche r
                                JOIN Effectuer ef ON r.id_recherche = ef.id_recherche
                                JOIN Etudiant e ON ef.num_etudiant = e.num_etudiant
                                WHERE r.statut = 'En attente'";
                        $demandes = $pdo->query($sql)->fetchAll();

                        foreach($demandes as $d): ?>
                        <tr>
                            <td><strong><?= strtoupper($d['nom']) ?> <?= $d['prenom'] ?></strong></td>
                            <td><?= $d['entreprise_contactee'] ?></td>
                            <td>
                                <a href="validation.php?id=<?= $d['id_recherche'] ?>" class="btn btn-sm btn-outline-primary">Examiner</a>
                                <a href="validation.php?valider=<?= $d['id_recherche'] ?>" class="btn btn-sm btn-success btn-confirm" data-confirm="Valider ce stage ?">Valider</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
            </table>
        </div>
    </div>
</div>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>