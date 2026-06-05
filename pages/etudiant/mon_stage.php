<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../../index.php'); exit(); }
$id_etud = $_SESSION['user_id'];

// --- TRAITEMENT DU SIGNALEMENT ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_incident'])) {
    $message_alerte = htmlspecialchars($_POST['message']);
    // On met à jour la colonne alerte_etudiant dans la table Stage
    $update = $pdo->prepare("UPDATE Stage SET alerte_etudiant = ? WHERE num_etudiant = ?");
    $update->execute([$message_alerte, $id_etud]);
    $success_msg = "Votre signalement a bien été transmis.";
}

// On récupère la recherche validée la plus récente
$sql = "SELECT r.* FROM recherche r 
        JOIN effectuer ef ON r.id_recherche = ef.id_recherche 
        WHERE ef.num_etudiant = ? AND r.statut = 'Validée' 
        ORDER BY r.id_recherche DESC LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_etud]);
$stage = $stmt->fetch();

// --- AJOUT SYNCHRONISATION CONVENTION ---
// On va chercher l'état réel de signature dans le Stage de l'étudiant
$stmtConvention = $pdo->prepare("SELECT convention_signee FROM Stage WHERE num_etudiant = ?");
$stmtConvention->execute([$id_etud]);
$info_stage = $stmtConvention->fetch();
$is_signee = ($info_stage && $info_stage['convention_signee'] === 'oui');
?>

<link rel="stylesheet" href="../../assets/css/style.css">
<div class="container py-5" style="max-width: 900px;">
    <?php if(isset($success_msg)): ?>
        <div class="alert alert-success shadow-sm border-0 mb-4 rounded-3">🚀 <?= $success_msg ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fw-bold m-0 text-dark" style="font-size: 2.2rem; letter-spacing: -0.5px;">Suivi de mon stage</h1>
        <?php if($stage): ?>
            <button class="btn btn-sm fw-bold px-3 py-2 text-uppercase d-flex align-items-center gap-2" 
                    data-bs-toggle="modal" data-bs-target="#modalIncident"
                    style="color: #DC3545; background-color: transparent; border: 1px solid #DC3545; font-size: 0.75rem; letter-spacing: 0.5px; border-radius: 6px;">
                <i class="bi bi-exclamation-triangle"></i> Signaler un problème
            </button>
        <?php endif; ?>
    </div>

    <?php if($stage): ?>
        <div class="text-center mx-auto" style="max-width: 650px;">
            
            <div class="card border-0 p-5 mb-4" style="border-radius: 24px; background-color: #f4f9f6;">
                <div class="card-body py-4">
                    <div class="mb-4 d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 90px; height: 90px; background-color: #198754 !important;">
                        <i class="bi bi-check-lg" style="font-size: 3.5rem;"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-3" style="font-size: 2.2rem; letter-spacing: -0.5px;">Félicitations !</h2>
                    <p class="fs-5 text-secondary m-0">
                        Stage chez <strong class="text-dark"><?= !empty($stage['entreprise_contactee']) ? htmlspecialchars($stage['entreprise_contactee']) : "Entreprise non renseignée" ?></strong>
                    </p>
                </div>
            </div>

            <div class="mb-5">
                <div class="text-primary fw-bold mb-3 d-flex align-items-center justify-content-center gap-2" style="color: #0066FF !important;">
                    <i class="bi bi-envelope-paper"></i> Coordonnées du Maître de Stage
                </div>
                
                <div class="p-2 mx-auto text-secondary" style="max-width: 450px; font-size: 0.95rem;">
                    <?php if(!empty($stage['reponses']) && $stage['reponses'] !== "0"): ?>
                        <div class="lh-base">
                            <?= nl2br(htmlspecialchars($stage['reponses'])) ?>
                        </div>
                    <?php else: ?>
                        <span class="text-muted italic">Aucune coordonnée enregistrée.</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4">
                <?php if($is_signee): ?>
                    <span class="badge px-4 py-3 fs-6 fw-bold text-white rounded-3 shadow-sm text-uppercase" style="background-color: #198754; font-size: 0.85rem !important; letter-spacing: 0.5px;">
                        Convention signée par le responsable
                    </span>
                <?php else: ?>
                    <span class="badge px-4 py-3 fs-6 fw-bold text-white rounded-3 shadow-sm text-uppercase" style="background-color: #198754; font-size: 0.85rem !important; letter-spacing: 0.5px;">
                        Convention en cours de signature
                    </span>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <div class="text-center py-5 my-5">
            <div class="mb-4 text-warning d-inline-flex align-items-center justify-content-center" style="color: #FFC107 !important;">
                <i class="bi bi-hourglass-split" style="font-size: 6rem;"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2" style="font-size: 1.8rem; letter-spacing: -0.5px;">Pas encore de stage validé</h3>
            <p class="text-muted mx-auto" style="max-width: 500px; font-size: 1.05rem;">
                Vos informations apparaîtront ici dès que le responsable aura validé votre demande.
            </p>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalIncident" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-octagon-fill"></i> Signaler une difficulté
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body px-4">
                    <p class="small text-muted mb-3">Expliquez brièvement le problème rencontré durant votre stage (missions, relationnel, etc.). Le responsable de stage en sera immédiatement informé.</p>
                    <textarea name="message" class="form-control bg-light border-0 rounded-3 p-3" rows="5" required placeholder="Votre message détaillé..."></textarea>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 m-0 fw-bold" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="send_incident" class="btn btn-danger rounded-pill px-4 m-0 fw-bold flex-grow-1 py-2">Envoyer au responsable</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>