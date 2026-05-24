<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

if ($_SESSION['role'] !== 'Responsable stage') {
    header('Location: ../../index.php');
    exit();
}

// 1. Calcul des statistiques globales
$total = $pdo->query("SELECT COUNT(*) FROM etudiant")->fetchColumn();
$valides = $pdo->query("SELECT COUNT(*) FROM stage")->fetchColumn();
$en_attente = $pdo->query("SELECT COUNT(*) FROM recherche WHERE statut = 'En attente'")->fetchColumn();
$en_recherche = $total - $valides;

$pourcentage = ($total > 0) ? round(($valides / $total) * 100) : 0;

// 2. Récupération des statistiques sur les problèmes (ton attribut "probleme")
$nb_problemes = $pdo->query("SELECT COUNT(*) FROM stage WHERE probleme IS NOT NULL AND probleme != ''")->fetchColumn();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Statistiques & Rapport Départemental</h2>
        <button onclick="window.print()" class="btn btn-outline-dark">
            <i class="bi bi-file-earmark-pdf"></i> Générer Rapport PDF
        </button>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-8">
            <div class="card p-4 shadow-sm border-0 h-100">
                <h5 class="fw-bold mb-3">Avancement Global des Recherches</h5>
                <div class="progress mb-3" style="height: 35px;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: <?= $pourcentage ?>%;">
                        <?= $pourcentage ?>%
                    </div>
                </div>
                <p class="text-muted small">Cible : 100% des étudiants placés avant le début des stages.</p>
                
                <div class="row text-center mt-3">
                    <div class="col-4 border-end">
                        <h4 class="fw-bold"><?= $valides ?></h4>
                        <small class="text-muted">Stages validés</small>
                    </div>
                    <div class="col-4 border-end">
                        <h4 class="fw-bold"><?= $en_recherche ?></h4>
                        <small class="text-muted">En recherche</small>
                    </div>
                    <div class="col-4">
                        <h4 class="fw-bold text-danger"><?= $nb_problemes ?></h4>
                        <small class="text-muted">Alertes / Problèmes</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 shadow-sm border-0 bg-light h-100">
                <h5 class="fw-bold mb-3 text-primary">Aide à la décision</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2">✅ <strong>Actions suggérées :</strong></li>
                    <?php if($pourcentage < 50): ?>
                        <li class="text-danger"><i class="bi bi-x-circle"></i> Organiser une réunion d'urgence avec les étudiants sans stage.</li>
                    <?php elseif($nb_problemes > 0): ?>
                        <li class="text-warning"><i class="bi bi-exclamation-triangle"></i> Contacter les tuteurs pour les <?= $nb_problemes ?> signalements.</li>
                    <?php else: ?>
                        <li class="text-success"><i class="bi bi-check-circle"></i> Relancer les signatures de conventions.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold">Détail des recherches par étudiant</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th>Étudiant</th>
                        <th>Statut</th>
                        <th>Entreprise (si trouvé)</th>
                        <th>Dernière Alerte</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // On liste tous les étudiants et on regarde s'ils ont un stage ou une recherche
                    $sql = "SELECT e.nom, e.prenom, s.lieu as stage_lieu, s.probleme, 
                            (SELECT r.statut FROM recherche r JOIN effectuer ef ON r.id_recherche = ef.id_recherche WHERE ef.num_etudiant = e.num_etudiant ORDER BY r.date_recherche DESC LIMIT 1) as dernier_statut
                            FROM etudiant e
                            LEFT JOIN stage s ON e.num_etudiant = s.num_etudiant";
                    foreach($pdo->query($sql) as $row): ?>
                    <tr>
                        <td><strong><?= strtoupper($row['nom']) ?></strong> <?= $row['prenom'] ?></td>
                        <td>
                            <?php if($row['stage_lieu']): ?>
                                <span class="badge bg-success">Placé</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">En recherche</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['stage_lieu'] ?? '---' ?></td>
                        <td class="text-danger small"><?= htmlspecialchars($row['probleme'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>