<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité : on vérifie que c'est bien le responsable
if ($_SESSION['role'] !== 'Responsable stage') {
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
        <span class="badge bg-danger p-2">Session Administrateur</span>
    </div>

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
                        <tbody>
                            <tr>
                                <td><strong>DUPONT Jean</strong></td>
                                <td>Ubisoft (Paris)</td>
                                <td>
                                    <a href="voir_demande.php?id=1" class="btn btn-sm btn-mmi">Examiner</a>
                                    <button class="btn btn-sm btn-success">Valider</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 border-top border-4 border-mmi">
                <h5>📢 Publier une offre</h5>
                <hr>
                <form action="publier_offre.php" method="POST">
                    <div class="mb-2">
                        <input type="text" name="titre" class="form-control" placeholder="Intitulé du poste" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="entreprise" class="form-control" placeholder="Entreprise">
                    </div>
                    <div class="mb-3">
                        <textarea name="mission" class="form-control" placeholder="Missions..." rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Publier l'offre</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>