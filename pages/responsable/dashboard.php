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
                        // Jointure entre Recherche, Effectuer et Etudiant selon ton SQL
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

        <div class="col-md-4">
            <div class="card p-4 border-top border-4 border-mmi">
                <h5>📢 Publier une offre</h5>
                <hr>
                <form action="traitement_offre.php" method="POST">
                    <div class="mb-2">
                        <label class="small fw-bold">Titre du poste</label>
                        <input type="text" name="intitule" class="form-control" placeholder="ex: Designer Web" required>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Entreprise / Contact</label>
                        <input type="text" name="contact" class="form-control" placeholder="nom de l'entreprise ou email" required>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Ville / Lieu</label>
                        <input type="text" name="lieu" class="form-control" placeholder="ex: Meaux (77)">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Description</label>
                        <textarea name="description" class="form-control" placeholder="Détails de l'offre..." rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Publier l'offre</button>
                </form>

                <form action="traitement_offre.php" method="POST">
                <div class="card mt-4 p-4 shadow-sm">
                    <h5><i class="bi bi-megaphone"></i> Mes dernières offres publiées</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Entreprise</th>
                                <th>Lieu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // On récupère les 5 dernières offres
                            $offres = $pdo->query("SELECT * FROM Offre ORDER BY id_offre DESC LIMIT 5")->fetchAll();
                            
                            foreach($offres as $o): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($o['intitule']) ?></strong></td>
                                <td><?= htmlspecialchars($o['contact']) ?></td>
                                <td><?= htmlspecialchars($o['lieu']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
    
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>