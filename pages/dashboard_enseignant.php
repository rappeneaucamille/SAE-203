<?php 
require_once '../includes/db.php';
include '../includes/header.php';

// Sécurité : on vérifie que c'est bien un enseignant standard
if ($_SESSION['role'] !== 'Enseignant standard') {
    header('Location: ../index.php');
    exit();
}
?>

<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Espace Consultation Enseignant</h2>

    <div class="row">
        <div class="col-md-12 mb-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0" style="color:var(--bleu-marine)">📋 Offres de stage publiées</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Intitulé</th>
                                <th>Entreprise</th>
                                <th>Lieu</th>
                                <th>Rémunération</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $offres = $pdo->query("SELECT * FROM Offre ORDER BY id_offre DESC");
                            while($o = $offres->fetch()): ?>
                            <tr>
                                <td><strong><?= $o['intitule'] ?></strong></td>
                                <td><?= $o['contact'] ?></td>
                                <td><?= $o['lieu'] ?></td>
                                <td><?= $o['remuneration'] ?> €</td>
                                <td><button class="btn btn-sm btn-outline-primary">Voir détails</button></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0" style="color:var(--bleu-marine)">🎓 Liste des Étudiants & Situations</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nom / Prénom</th>
                                <th>Promotion</th>
                                <th>Groupe</th>
                                <th>Contact</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $etudiants = $pdo->query("SELECT * FROM Etudiant ORDER BY nom ASC");
                            while($e = $etudiants->fetch()): ?>
                            <tr>
                                <td><?= strtoupper($e['nom']) ?> <?= $e['prenom'] ?></td>
                                <td><?= $e['promotion'] ?></td>
                                <td><?= $e['groupe_TD'] ?> / <?= $e['groupe_TP'] ?></td>
                                <td><?= $e['identifiant'] ?></td>
                                <td>
                                    <span class="badge bg-secondary">Inscrit</span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>