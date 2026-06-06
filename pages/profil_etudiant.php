<?php
require_once '../includes/db.php';
include '../includes/header.php';

// SÉCURITÉ : Tous les profs et admins peuvent voir, mais pas les étudiants anonymes
if (!isset($_SESSION['role']) || $_SESSION['role'] === 'Etudiant') {
    header('Location: ../index.php');
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Aucun étudiant spécifié.</div></div>";
    include '../includes/footer.php';
    exit();
}

$num_etud = $_GET['id'];

// 1. RÉCUPÉRATION DES INFOS PERSO DE L'ÉTUDIANT
$stmtEtud = $pdo->prepare("SELECT * FROM Etudiant WHERE num_etudiant = ? OR LOWER(identifiant) = LOWER(?)");
$stmtEtud->execute([$num_etud, $num_etud]);
$etudiant = $stmtEtud->fetch();

if (!$etudiant) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Étudiant introuvable.</div></div>";
    include '../includes/footer.php';
    exit();
}

// On réassigne le vrai numéro d'étudiant pour les requêtes suivantes
$vrai_num_etud = $etudiant['num_etudiant'];

// 2. RÉCUPÉRATION DU STAGE ET DU MAÎTRE DE STAGE
$stmtStage = $pdo->prepare("
    SELECT s.*, m.nom as mds_nom, m.prenom as mds_prenom, m.email as mds_email 
    FROM Stage s 
    LEFT JOIN Maitre_Stage m ON s.id_maitre = m.id_maitre 
    WHERE s.num_etudiant = ?
");
$stmtStage->execute([$vrai_num_etud]);
$stage = $stmtStage->fetch();

// 3. RÉCUPÉRATION DE L'ENTREPRISE (via la recherche validée ou liée au stage)
$stmtRecherche = $pdo->prepare("
    SELECT r.*, ent.nom as ent_nom, ent.adresse as ent_adresse, ent.contact as ent_contact
    FROM recherche r
    JOIN effectuer ef ON r.id_recherche = ef.id_recherche
    LEFT JOIN entreprise ent ON r.entreprise_contactee = ent.nom
    WHERE ef.num_etudiant = ? AND r.statut = 'Validée'
    ORDER BY r.id_recherche DESC LIMIT 1
");
$stmtRecherche->execute([$vrai_num_etud]);
$recherche = $stmtRecherche->fetch();

// 4. RÉCUPÉRATION DE LA SOUTENANCE ET DES NOTES
$stmtSoutenance = $pdo->prepare("
    SELECT s.*, j.enseignant_1, j.enseignant_2 
    FROM soutenance s
    LEFT JOIN jury j ON s.id_jury = j.id_jury
    WHERE LOWER(s.etudiant) = LOWER(?)
");
$stmtSoutenance->execute([$etudiant['identifiant']]);
$soutenance = $stmtSoutenance->fetch();
?>

<link rel="stylesheet" href="../assets/css/style.css">
<div class="container py-5" style="max-width: 1000px;">
    
    <div class="mb-4">
        <a href="javascript:history.back()" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm text-secondary">
            <i class="bi bi-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <div class="card border-0 shadow-sm p-4 mb-5 rounded-4 bg-white">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 70px; height: 70px; font-size: 1.8rem; background-color: #2F448A !important;">
                <?= strtoupper(substr($etudiant['nom'], 0, 1) . substr($etudiant['prenom'], 0, 1)) ?>
            </div>
            <div>
                <h1 class="fw-bold m-0 text-dark" style="font-size: 2rem;"><?= strtoupper(htmlspecialchars($etudiant['nom'])) ?> <?= htmlspecialchars($etudiant['prenom']) ?></h1>
                <p class="text-muted m-0">Numéro étudiant : <strong><?= htmlspecialchars($etudiant['num_etudiant']) ?></strong> | Promotion : <span class="badge bg-light text-dark border"><?= htmlspecialchars($etudiant['promotion'] ?? 'Non assignée') ?></span></p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 p-4 rounded-4 bg-white">
                <h4 class="fw-bold text-primary mb-4" style="color: #0066FF !important;">
                    <i class="bi bi-building-fill me-2"></i> Informations du Stage
                </h4>
                
                <?php if ($recherche || $stage): ?>
                    <div class="mb-3">
                        <label class="small text-muted fw-bold d-block">Entreprise affectée</label>
                        <span class="fs-5 fw-bold text-dark"><?= htmlspecialchars($recherche['entreprise_contactee'] ?? $stage['entreprise_contactee'] ?? 'Non spécifiée') ?></span>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted fw-bold d-block">Adresse Entreprise</label>
                        <span class="text-secondary"><?= nl2br(htmlspecialchars($recherche['ent_adresse'] ?? 'Non renseignée')) ?></span>
                    </div>

                    <hr class="text-muted border-light my-3">

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="small text-muted fw-bold d-block">Date de Début</label>
                            <span class="text-dark fw-medium"><i class="bi bi-calendar-event me-1"></i> <?= !empty($stage['date_debut']) ? date('d/m/Y', strtotime($stage['date_debut'])) : 'À renseigner' ?></span>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="small text-muted fw-bold d-block">Date de Fin</label>
                            <span class="text-dark fw-medium"><i class="bi bi-calendar-check me-1"></i> <?= !empty($stage['date_fin']) ? date('d/m/Y', strtotime($stage['date_fin'])) : 'À renseigner' ?></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted fw-bold d-block">Numéro de Convention</label>
                        <span class="badge bg-dark px-3 py-2 fs-6">N° <?= htmlspecialchars($stage['id_stage'] ?? 'En attente') ?></span>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted fw-bold d-block">Statut Signature Convention</label>
                        <?php if(isset($stage['convention_signee']) && $stage['convention_signee'] === 'oui'): ?>
                            <span class="badge bg-success text-white px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Signée par le Responsable</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark px-3 py-2"><i class="bi bi-hourglass-split me-1"></i> En cours de signature</span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted italic">
                        <i class="bi bi-slash-circle fs-2 d-block mb-2 text-warning"></i> Aucun stage validé ou affecté pour le moment.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 p-4 rounded-4 bg-white">
                <h4 class="fw-bold text-success mb-4">
                    <i class="bi bi-person-badge-fill me-2"></i> Encadrement & Visites
                </h4>

                <h6 class="fw-bold text-secondary mb-3">Maître de Stage</h6>
                <?php if (!empty($recherche['reponses']) && $recherche['reponses'] !== "0"): ?>
                    <div class="bg-light p-3 rounded-3 mb-4 border-0 lh-base text-secondary" style="font-size: 0.95rem;">
                        <?= nl2br(htmlspecialchars($recherche['reponses'])) ?>
                    </div>
                <?php elseif (!empty($stage['mds_nom'])): ?>
                    <div class="bg-light p-3 rounded-3 mb-4 border-0 text-secondary">
                        <strong>NOM :</strong> <?= strtoupper(htmlspecialchars($stage['mds_nom'])) ?><br>
                        <strong>PRÉNOM :</strong> <?= htmlspecialchars($stage['mds_prenom']) ?><br>
                        <strong>EMAIL :</strong> <?= htmlspecialchars($stage['mds_email']) ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border text-muted small mb-4">
                        <i class="bi bi-info-circle me-1"></i> Coordonnées du maître de stage non communiquées par l'étudiant.
                    </div>
                <?php endif; ?>

                <hr class="text-muted border-light my-3">

                <h6 class="fw-bold text-secondary mb-3">Suivi pédagogique (Dates de visites)</h6>
                <div class="mb-3">
                    <label class="small text-muted fw-bold d-block">Date prévue de visite mi-parcours</label>
                    <span class="text-dark fw-medium">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> 
                        <?= !empty($stage['date_visite']) ? date('d/m/Y', strtotime($stage['date_visite'])) : 'Non programmée actuellement' ?>
                    </span>
                </div>
                
                <?php if($_SESSION['role'] === 'Administrateur' || $_SESSION['role'] === 'Responsable stage'): ?>
                    <form method="POST" action="" class="mt-3 row g-2 align-items-center">
                        </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h4 class="fw-bold text-dark mb-4">
                    <i class="bi bi-journal-check text-warning me-2"></i> Résultats des Évaluations & Soutenance
                </h4>

                <?php if ($soutenance): ?>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center border-0 shadow-sm">
                                <label class="small text-muted fw-bold d-block mb-1">Note Rapport Écrit</label>
                                <span class="fs-3 fw-bold text-primary"><?= isset($soutenance['note_rapport']) ? htmlspecialchars($soutenance['note_rapport']) . " / 20" : "—" ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center border-0 shadow-sm">
                                <label class="small text-muted fw-bold d-block mb-1">Note Soutenance Oral</label>
                                <span class="fs-3 fw-bold text-success"><?= isset($soutenance['note_soutenance']) ? htmlspecialchars($soutenance['note_soutenance']) . " / 20" : "—" ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 text-center border-0 shadow-sm text-white" style="background-color: #2F448A;">
                                <label class="small text-light-50 fw-bold d-block mb-1 text-white-50">Moyenne Générale</label>
                                <span class="fs-3 fw-bold">
                                    <?php 
                                    if(isset($soutenance['note_rapport']) && isset($soutenance['note_soutenance'])) {
                                        echo (($soutenance['note_rapport'] + $soutenance['note_soutenance']) / 2) . " / 20";
                                    } else {
                                        echo "—";
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 border-top border-light pt-3 text-secondary small d-flex flex-wrap gap-4">
                        <span><i class="bi bi-calendar3 me-1"></i> Date Soutenance : <strong><?= date('d/m/Y', strtotime($soutenance['date_soutenance'])) ?></strong></span>
                        <span><i class="bi bi-clock me-1"></i> Horaire : <strong><?= substr($soutenance['heure_debut'], 0, 5) ?> - <?= substr($soutenance['heure_fin'], 0, 5) ?></strong></span>
                        <span><i class="bi bi-door-open me-1"></i> Salle : <strong><?= htmlspecialchars($soutenance['salle']) ?></strong></span>
                    </div>

                    <div class="mt-3 bg-light p-3 rounded-3 text-muted border-0 small">
                        <i class="bi bi-people-fill me-1"></i> Membres du Jury affectés : 
                        <strong><?= htmlspecialchars($soutenance['enseignant_1']) ?></strong> & <strong><?= htmlspecialchars($soutenance['enseignant_2']) ?></strong>
                    </div>

                <?php else: ?>
                    <div class="alert alert-light text-muted border-0 shadow-sm p-3 m-0">
                        <i class="bi bi-info-circle-fill text-warning me-2"></i> Aucune planification de soutenance ni de notes enregistrées pour cet étudiant à ce jour.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>