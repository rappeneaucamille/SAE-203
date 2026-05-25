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
?>

<div class="container py-5">
    <?php if(isset($success_msg)): ?>
        <div class="alert alert-danger shadow-sm border-0 mb-4">⚠️ <?= $success_msg ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0" style="color: var(--mmi-blue);">Suivi de mon stage</h2>
        <?php if($stage): ?>
            <button class="btn btn-outline-danger btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalIncident">
                <i class="bi bi-exclamation-triangle"></i> SIGNALER UN PROBLÈME
            </button>
        <?php endif; ?>
    </div>

    <?php if($stage): ?>
        <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 20px; background-color: var(--pastel-blue);">
            <div class="mb-4"><i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i></div>
            <h2 class="fw-bold">Félicitations !</h2>
            
            <p class="fs-5">Stage chez <strong><?= !empty($stage['entreprise_contactee']) ? htmlspecialchars($stage['entreprise_contactee']) : "Entreprise non renseignée" ?></strong></p>
            
            <hr class="w-50 mx-auto">

            <div class="mt-4 p-4 bg-white bg-opacity-50 rounded-3 mx-auto text-start" style="max-width: 500px;">
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-badge"></i> Coordonnées du Maître de Stage</h6>
                
                <?php if(!empty($stage['reponses']) && $stage['reponses'] !== "0"): ?>
                    <div class="small text-dark">
                        <?= nl2br(htmlspecialchars($stage['reponses'])) ?>
                    </div>
                <?php else: ?>
                    <p class="small text-muted italic m-0">
                        Aucune coordonnée enregistrée. <br>
                    </p>
                <?php endif; ?>
            </div>

            <div class="mt-4">
                <span class="badge bg-success p-2 px-4">Convention en cours de signature</span>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 20px; background-color: #f8f9fa;">
            <div class="mb-4"><i class="bi bi-hourglass-split text-warning" style="font-size: 5rem;"></i></div>
            <h3>Pas encore de stage validé</h3>
            <p class="text-muted">Vos informations apparaîtront ici dès que le responsable aura validé votre demande.</p>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalIncident" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Signaler une difficulté</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <p class="small text-muted">Expliquez brièvement le problème rencontré durant votre stage (missions, relationnel, etc.).</p>
                    <textarea name="message" class="form-control" rows="5" required placeholder="Votre message..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="send_incident" class="btn btn-danger">Envoyer au responsable</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>