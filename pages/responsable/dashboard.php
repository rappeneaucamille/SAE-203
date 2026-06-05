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

<link rel="stylesheet" href="../../assets/css/style.css">

<div class="container py-5" style="max-width: 1140px;">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fw-bold m-0" style="color: #2E4588; font-size: 2.2rem; letter-spacing: -0.5px;">Tableau de Bord Responsable</h1>
        <span class="badge px-4 py-2 rounded-3 fw-bold text-white shadow-sm" style="background-color: #DC3545; font-size: 0.85rem; letter-spacing: 0.5px;">SESSION RESPONSABLE</span>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card p-4 border-0 text-white shadow-sm text-center d-flex flex-column align-items-center justify-content-center" style="background-color: #7F8EB3; border-radius: 20px; min-height: 140px;">
                <span class="text-uppercase fw-bold opacity-75" style="font-size: 0.8rem;  color: white !important;letter-spacing: 0.8px;">Total Étudiants</span>
                <h2 class="fw-bold mt-2 mb-0" style="font-size: 2.8rem; letter-spacing: -1px;"><?= $totalEtudiants ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 border-0 text-white shadow-sm text-center d-flex flex-column align-items-center justify-content-center" style="background-color: #71B999; border-radius: 20px; min-height: 140px;">
                <span class="text-uppercase fw-bold opacity-75" style="font-size: 0.8rem; letter-spacing: 0.8px;">Stages Validés</span>
                <h2 class="fw-bold mt-2 mb-0" style="font-size: 2.8rem; letter-spacing: -1px;"><?= $stagesValides ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 border-0 text-white shadow-sm text-center d-flex flex-column align-items-center justify-content-center" style="background-color: #F5CD5F; border-radius: 20px; min-height: 140px;">
                <span class="text-uppercase fw-bold opacity-75" style="font-size: 0.8rem; letter-spacing: 0.8px;">En recherche</span>
                <h2 class="fw-bold mt-2 mb-0" style="font-size: 2.8rem; letter-spacing: -1px;"><?= $enRecherche ?></h2>
            </div>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-12">
            <div class="bg-white border-0" style="border-radius: 24px; overflow: hidden; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.07), 0 5px 15px rgba(0, 0, 0, 0.04);">
                <div class="p-4 border-bottom bg-white">
                    <h5 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-hourglass-split text-warning"></i> Demandes de validation en attente
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-secondary fw-bold" style="font-size: 0.85rem; text-uppercase; letter-spacing: 0.5px;">
                                <th class="py-3 ps-4 border-0">Étudiant</th>
                                <th class="py-3 border-0">Entreprise</th>
                                <th class="py-3 pe-4 border-0 text-end" style="width: 240px;">Action</th>
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
                                <tr><td colspan="3" class="text-center text-muted py-5 fs-6">Aucune demande en attente pour le moment.</td></tr>
                            <?php else: 
                                foreach($demandes as $d): ?>
                                <tr>
                                    <td class="py-4 ps-4 fw-bold text-dark" style="font-size: 1.05rem;">
                                        <span class="text-uppercase"><?= htmlspecialchars($d['nom']) ?></span> <?= htmlspecialchars($d['prenom']) ?>
                                    </td>
                                    <td class="text-secondary fw-semibold"><?= htmlspecialchars($d['entreprise_contactee']) ?></td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="validation.php?id=<?= $d['id_recherche'] ?>" class="btn btn-sm btn-outline-primary px-3 fw-bold rounded-2">Examiner</a>
                                            <a href="validation.php?valider=<?= $d['id_recherche'] ?>" class="btn btn-sm text-white px-3 fw-bold btn-confirm rounded-2" data-confirm="Valider ce stage ?" style="background-color: #71B999;">Valider</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; 
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="bg-white border-0" style="border-radius: 24px; overflow: hidden; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.07), 0 5px 15px rgba(0, 0, 0, 0.04);">
                <div class="p-4 bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-calendar3 text-primary"></i> Planning Général des Soutenances
                    </h5>
                    <a href="soutenance.php" class="btn btn-primary btn-sm fw-bold px-4 py-2 rounded-3 shadow-sm" style="background-color: #0066FF; border: none;">
                        <i class="bi bi-calendar-plus me-1"></i> Programmer une soutenance
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr style="background-color: #4A4A4A !important;">
                                <th class="py-3 ps-4 border-0 text-white fw-bold" style="font-size: 0.9rem; background: transparent;">Date & Horaires</th>
                                <th class="py-3 border-0 text-white fw-bold" style="font-size: 0.9rem; background: transparent;">Étudiant Convoqué</th>
                                <th class="py-3 border-0 text-white fw-bold text-center" style="font-size: 0.9rem; background: transparent;">Salle</th>
                                <th class="py-3 pe-4 border-0 text-white fw-bold" style="font-size: 0.9rem; background: transparent;">Membres du Jury</th>
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
                                <tr><td colspan="4" class="text-center text-muted py-5">Aucune soutenance planifiée pour le moment.</td></tr>
                            <?php else:
                                foreach ($soutenances as $s): ?>
                                <tr>
                                    <td class="py-4 ps-4">
                                        <div class="fw-bold text-dark" style="font-size: 1.05rem;"><?= date('d/m/Y', strtotime($s['date_soutenance'])) ?></div>
                                        <small class="text-muted fw-bold"><?= substr($s['heure_debut'], 0, 5) ?> - <?= substr($s['heure_fin'], 0, 5) ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark text-uppercase"><?= htmlspecialchars($s['etud_nom'] ?? 'Inconnu') ?> <span class="text-capitalize fw-normal"><?= htmlspecialchars($s['etud_prenom'] ?? '') ?></span></div>
                                        <small class="text-muted"><?= htmlspecialchars($s['etud_email']) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge px-3 py-2 fw-bold text-white" style="background-color: #6C757D; border-radius: 6px; font-size: 0.8rem;"><?= htmlspecialchars($s['salle']) ?></span>
                                    </td>
                                    <td class="pe-4 py-3">
                                        <div class="fw-semibold text-dark small">1. <?= htmlspecialchars($s['enseignant_1'] ?? 'Non assigné') ?></div>
                                        <div class="text-muted extra-small">2. <?= htmlspecialchars($s['enseignant_2'] ?? 'Non assigné') ?></div>
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

<script>
// Confirmation pour les boutons de validation directe
document.querySelectorAll('.btn-confirm').forEach(button => {
    button.addEventListener('click', function(e) {
        if(!confirm(this.getAttribute('data-confirm'))) {
            e.preventDefault();
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>