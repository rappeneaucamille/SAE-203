<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : On autorise le Responsable OU l'Admin
if ($_SESSION['role'] !== 'Responsable stage' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// 1. Calcul des statistiques globales
$total = $pdo->query("SELECT COUNT(*) FROM etudiant")->fetchColumn();
$valides = $pdo->query("SELECT COUNT(*) FROM stage")->fetchColumn();
$en_attente = $pdo->query("SELECT COUNT(*) FROM recherche WHERE statut = 'En attente'")->fetchColumn();
$en_recherche = $total - $valides;

$pourcentage = ($total > 0) ? round(($valides / $total) * 100) : 0;

// 2. Récupération des statistiques sur les problèmes
$nb_problemes = $pdo->query("SELECT COUNT(*) FROM stage WHERE probleme IS NOT NULL AND probleme != ''")->fetchColumn();
?>

<link rel="stylesheet" href="../../assets/css/style.css">

<div class="container py-5" style="max-width: 1140px;">
    
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <h1 class="fw-bold m-0" style="color: #000000; font-size: 2.2rem; letter-spacing: -0.5px;">
            Statistiques & Rapport Départemental
        </h1>
        <button onclick="window.print()" class="btn btn-outline-dark fw-medium d-inline-flex align-items-center gap-2 px-3 py-2" style="border-radius: 6px; font-size: 0.9rem;">
            <i class="bi bi-file-earmark-pdf"></i> Générer Rapport PDF
        </button>
    </div>

    <div class="row g-4 mb-5">
        
        <div class="col-lg-7">
            <div class="bg-white p-4 border-0 h-100" style="border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.05);">
                <h5 class="fw-bold mb-3" style="color: #000000; font-size: 1.25rem;">Avancement Global des Recherches</h5>
                
                <div class="progress mb-2" style="height: 30px; border-radius: 6px; background-color: #F1F5F9;">
                    <div class="progress-bar fw-bold d-flex align-items-center justify-content-center" 
                         role="progressbar" 
                         style="width: <?= $pourcentage ?>%; background-color: #5CB887; font-size: 0.9rem;" 
                         aria-valuenow="<?= $pourcentage ?>" aria-valuemin="0" aria-valuemax="100">
                        <?= $pourcentage ?>%
                    </div>
                </div>
                <p class="text-secondary small mb-4">Cible : 100% des étudiants placés avant le début des stages.</p>
                
                <div class="row text-center pt-2">
                    <div class="col-4" style="border-right: 1px solid #E2E8F0;">
                        <h2 class="fw-bold mb-1" style="color: #5CB887; font-size: 2.5rem;"><?= $valides ?></h2>
                        <span class="text-secondary small">Stage validés</span>
                    </div>
                    <div class="col-4" style="border-right: 1px solid #E2E8F0;">
                        <h2 class="fw-bold mb-1" style="color: #F4B942; font-size: 2.5rem;"><?= $en_recherche ?></h2>
                        <span class="text-secondary small">En recherche</span>
                    </div>
                    <div class="col-4">
                        <h2 class="fw-bold mb-1" style="color: #D93838; font-size: 2.5rem;"><?= $nb_problemes ?></h2>
                        <span class="text-secondary small">Problèmes</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="p-4 border-0 h-100" style="border-radius: 20px; background-color: #F8FAFC; box-shadow: 0 15px 35px rgba(0,0,0,0.02);">
                <h5 class="fw-bold mb-3" style="color: #1E3A8A; font-size: 1.25rem;">Aide à la décision</h5>
                <div class="d-flex flex-column gap-2">
                    <span class="text-dark fw-bold small d-block">
                        <i class="bi bi-check-all text-success me-1"></i> Actions suggérées :
                    </span>
                    
                    <div class="p-3 bg-white rounded border-0 shadow-sm mt-1">
                        <?php if($pourcentage < 50): ?>
                            <div class="text-danger small d-flex align-items-center gap-2">
                                <i class="bi bi-x-circle-fill fs-5"></i> Organiser une réunion d'urgence avec les étudiants sans stage.
                            </div>
                        <?php elseif($nb_problemes > 0): ?>
                            <div class="text-warning small d-flex align-items-center gap-2" style="color: #D97706 !important;">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i> Contacter les tuteurs pour les <?= $nb_problemes ?> signalements.
                            </div>
                        <?php else: ?>
                            <div class="text-success small d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill fs-5"></i> Relancer les signatures de conventions.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3 class="fw-bold mb-4" style="font-size: 1.4rem; color: #000000; letter-spacing: -0.3px;">Détail des recherches par étudiant</h3>
    
    <div class="border-0 shadow-sm overflow-hidden" style="border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.05) !important;">
        <div class="table-responsive">
            <table class="table align-middle mb-0 bg-white">
                <thead style="font-size: 0.9rem;">
                    <tr>
                        <th class="ps-4 py-3 fw-semibold" style="background-color: rgba(0, 0, 0, 0.78); color: #FFFFFF !important;">Étudiant</th>
                        <th class="py-3 fw-semibold text-center" style="background-color: rgba(0, 0, 0, 0.78); color: #FFFFFF !important;">Promotion</th>
                        <th class="py-3 fw-semibold" style="background-color: rgba(0, 0, 0, 0.78); color: #FFFFFF !important;">Entreprise</th>
                        <th class="py-3 fw-semibold text-center" style="background-color: rgba(0, 0, 0, 0.78); color: #FFFFFF !important;">Statut</th>
                        <th class="pe-4 py-3 fw-semibold text-end" style="background-color: rgba(0, 0, 0, 0.78); color: #FFFFFF !important;">Dernière alerte</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.95rem; color: #334155;">
                    <?php
                    $sql = "SELECT e.nom, e.prenom, e.promotion, s.lieu as stage_lieu, s.probleme, 
                            (SELECT r.statut FROM recherche r JOIN effectuer ef ON r.id_recherche = ef.id_recherche WHERE ef.num_etudiant = e.num_etudiant ORDER BY r.date_recherche DESC LIMIT 1) as dernier_statut
                            FROM etudiant e
                            LEFT JOIN stage s ON e.num_etudiant = s.num_etudiant
                            ORDER BY e.nom ASC";
                    
                    foreach($pdo->query($sql) as $row): 
                        $promo = htmlspecialchars($row['promotion'] ?? 'MMI1');
                    ?>
                    <tr style="border-bottom: 1px solid #F1F5F9;">
                        <td class="ps-4 py-3">
                            <strong class="text-dark"><?= strtoupper(htmlspecialchars($row['nom'])) ?></strong> 
                            <span class="text-secondary"><?= htmlspecialchars($row['prenom']) ?></span>
                        </td>
                        
                        <td class="py-3 text-center">
                            <span class="badge px-3 py-2 fw-semibold" style="background-color: #64748B; color: #FFFFFF; border-radius: 6px; font-size: 0.75rem;">
                                <?= $promo ?>
                            </span>
                        </td>
                        
                        <td class="py-3 text-secondary">
                            <?= $row['stage_lieu'] ? htmlspecialchars($row['stage_lieu']) : '<span class="text-muted opacity-50">---</span>' ?>
                        </td>
                        
                        <td class="py-3 text-center">
                            <?php if($row['stage_lieu']): ?>
                                <span class="badge px-3 py-2 fw-medium d-inline-flex align-items-center gap-1" 
                                      style="background-color: #E6F4EA; color: #137333; border-radius: 30px; font-size: 0.85rem;">
                                    <i class="bi bi-check-circle" style="font-size: 0.75rem;"></i> Placé
                                </span>
                            <?php else: ?>
                                <span class="badge px-3 py-2 fw-medium d-inline-flex align-items-center gap-1" 
                                      style="background-color: #FEF3C7; color: #D97706; border-radius: 30px; font-size: 0.85rem;">
                                    <i class="bi bi-clock" style="font-size: 0.75rem;"></i> En recherche
                                </span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="pe-4 py-3 text-end text-danger fw-medium">
                            <?= $row['probleme'] ? htmlspecialchars($row['probleme']) : '<span class="text-muted opacity-60">Aucune</span>' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>