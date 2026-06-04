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

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Tableau de Bord Responsable</h2>
        <span class="badge bg-danger">Session Responsable</span>
    </div>

    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card text-center p-3 border-0 shadow-sm text-white" style="background-color: rgba(46, 69, 136, 0.6)">
                <h6>Total Étudiants</h6>
                <h2 class="fw-bold"><?= $totalEtudiants ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-3 border-0 shadow-sm text-white" style="background-color: rgba(25, 135, 84, 0.6)">
                <h6>Stages Validés</h6>
                <h2 class="fw-bold"><?= $stagesValides ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-3 border-0 shadow-sm text-white" style="background-color: rgba(28, 129, 253, 0.6)">
                <h6>En recherche</h6>
                <h2 class="fw-bold"><?= $enRecherche ?></h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-12 mb-4">
            <div class="card p-4 shadow-sm border-0">
                <h5 class="mb-4 fw-bold">⏳ Demandes de validation en attente</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Étudiant</th>
                                <th>Entreprise</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT r.*, e.nom, e.prenom 
                                    FROM Recherche r
                                    JOIN Effectuer ef ON r.id_recherche = ef.id_recherche
                                    JOIN Etudiant e ON ef.num_etudiant = e.num_etudiant
                                    WHERE r.statut = 'En attente'";
                            $demandes = $pdo->query($sql)->fetchAll();

                            if (empty($demandes)): ?>
                                <tr><td colspan="3" class="text-center text-muted py-3">Aucune demande en attente.</td></tr>
                            <?php else: 
                                foreach($demandes as $d): ?>
                                <tr>
                                    <td><strong><?= strtoupper($d['nom']) ?> <?= $d['prenom'] ?></strong></td>
                                    <td><?= htmlspecialchars($d['entreprise_contactee']) ?></td>
                                    <td>
                                        <a href="validation.php?id=<?= $d['id_recherche'] ?>" class="btn btn-sm btn-outline-primary">Examiner</a>
                                        <a href="validation.php?valider=<?= $d['id_recherche'] ?>" class="btn btn-sm btn-success btn-confirm" data-confirm="Valider ce stage ?">Valider</a>
                                    </td>
                                </tr>
                                <?php endforeach; 
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card p-4 shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="m-0 fw-bold" style="color: #2e4588;"><i class="bi bi-calendar3"></i> Planning Général des Soutenances</h5>
                    <a href="soutenance.php" class="btn btn-primary btn-sm fw-bold">
                        <i class="bi bi-calendar-plus"></i> Programmer une soutenance
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Date & Horaires</th>
                                <th>Étudiant Convoqué</th>
                                <th>Salle</th>
                                <th>Membres du Jury</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sqlSout = "SELECT s.date_soutenance, s.heure_debut, s.heure_fin, s.salle,
                                               e.nom AS etud_nom, e.prenom AS etud_prenom, s.etudiant AS etud_email,
                                               j.enseignant_1, j.enseignant_2
                                        FROM soutenance s
                                        INNER JOIN etudiant e ON LOWER(s.etudiant) = LOWER(e.identifiant)
                                        LEFT JOIN jury j ON s.id_jury = j.id_jury
                                        ORDER BY s.date_soutenance ASC, s.heure_debut ASC";
                            $soutenances = $pdo->query($sqlSout)->fetchAll();

                            if (empty($soutenances)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Aucune soutenance planifiée pour le moment.</td></tr>
                            <?php else:
                                foreach ($soutenances as $s): ?>
                                <tr>
                                    <td>
                                        <strong><?= date('d/m/Y', strtotime($s['date_soutenance'])) ?></strong><br>
                                        <small class="text-muted"><?= substr($s['heure_debut'], 0, 5) ?> - <?= substr($s['heure_fin'], 0, 5) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= strtoupper($s['etud_nom'] ?? 'Inconnu') ?></strong> <?= $s['etud_prenom'] ?? '' ?><br>
                                        <small class="text-muted"><?= htmlspecialchars($s['etud_email']) ?></small>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($s['salle']) ?></span></td>
                                    <td>
                                        <div class="small">1. <?= htmlspecialchars($s['enseignant_1'] ?? 'Non assigné') ?></div>
                                        <div class="small text-muted">2. <?= htmlspecialchars($s['enseignant_2'] ?? 'Non assigné') ?></div>
                                    </td>
                                </tr>
                                <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../../includes/footer.php'; ?>