<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

if (!isset($_SESSION['user_id'])) { 
    header('Location: ../../index.php'); 
    exit(); 
}
$id_etud = $_SESSION['user_id'];

// --- TRAITEMENT DU SIGNALEMENT  ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_incident'])) {
    $message_alerte = htmlspecialchars($_POST['message']);
    $update = $pdo->prepare("UPDATE Stage SET alerte_etudiant = ? WHERE num_etudiant = ?");
    $update->execute([$message_alerte, $id_etud]);
    $success_msg = "Votre signalement a bien été transmis.";
}

// --- TRAITEMENT DE L'ENREGISTREMENT DU MAÎTRE DE STAGE  ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_tuteur'])) {
    $nom_tuteur = trim(htmlspecialchars($_POST['nom_tuteur']));
    $prenom_tuteur = trim(htmlspecialchars($_POST['prenom_tuteur']));
    $email_tuteur = trim(htmlspecialchars($_POST['email_tuteur']));
    
    $insMaitre = $pdo->prepare("INSERT INTO Maitre_stage (Nom, Prenom, Email) VALUES (?, ?, ?)");
    $insMaitre->execute([$nom_tuteur, $prenom_tuteur, $email_tuteur]);
    $id_maitre = $pdo->lastInsertId();
    
    $updateStage = $pdo->prepare("UPDATE Stage SET id_maitre = ? WHERE num_etudiant = ?");
    $updateStage->execute([$id_maitre, $id_etud]);
    header("Location: mon_stage.php");
    exit();
}

// Récupération complète du stage
$query = "SELECT s.*, e.Nom AS nom_etud, e.Prenom AS prenom_etud, e.promotion, ent.Nom AS nom_ent, ent.Adresse AS adresse_ent, m.Nom AS nom_maitre, m.Prenom AS prenom_maitre
          FROM Stage s
          JOIN etudiant e ON s.num_etudiant = e.num_etudiant
          LEFT JOIN Entreprise ent ON s.id_ent = ent.id_ent
          LEFT JOIN Maitre_stage m ON s.id_maitre = m.id_maitre
          WHERE s.num_etudiant = ? LIMIT 1";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_etud]);
$stage = $stmt->fetch(PDO::FETCH_ASSOC);

// Décoder les données JSON de la convention
$convention_data = [];
if ($stage && !empty($stage['description'])) {
    $decoded = json_decode($stage['description'], true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $convention_data = $decoded;
    }
}

// Détermination propre du nom de l'entreprise pour l'affichage
$affichage_entreprise = "Non renseignée";
if ($stage) {
    if (!empty($stage['nom_ent'])) {
        $affichage_entreprise = $stage['nom_ent'];
    } elseif (!empty($stage['lieu'])) {
        $affichage_entreprise = $stage['lieu'];
    }
}

// Sécurité pour le formatage des dates
$date_debut_formatee = "";
$date_fin_formatee = "";
if ($stage) {
    if (!empty($stage['Date_debut'])) {
        $date_debut_formatee = date('d/m/Y', strtotime($stage['Date_debut']));
    } elseif (!empty($stage['date_debut'])) {
        $date_debut_formatee = date('d/m/Y', strtotime($stage['date_debut']));
    }
    
    if (!empty($stage['Date_fin'])) {
        $date_fin_formatee = date('d/m/Y', strtotime($stage['Date_fin']));
    } elseif (!empty($stage['date_fin'])) {
        $date_fin_formatee = date('d/m/Y', strtotime($stage['date_fin']));
    }
}
?>

<link rel="stylesheet" href="../../assets/css/style.css">
<div class="container py-5" style="max-width: 900px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold m-0" style="color: #2E4588; font-size: 2.2rem; letter-spacing: -0.5px;">Mon Stage</h1>
    </div>

    <?php if (!$stage): ?>
        <div class="card border-0 p-5 text-center bg-white shadow-sm mb-5" style="border-radius: 20px;">
            <i class="bi bi-hourglass-split text-warning mb-3" style="font-size: 3.5rem;"></i>
            <h4 class="fw-bold text-dark">Stage en attente de validation</h4>
            <p class="text-muted m-0 mb-2">Votre demande de stage a bien été transmise. Le responsable des stages examine actuellement votre fiche de recherche pour l'accepter.</p>
        </div>
    <?php else: ?>
        <div class="card border-0 p-4 bg-white shadow-sm mb-5" style="border-radius: 20px; border-left: 6px solid #198754 !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-check-lg" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-dark m-0">✓ Stage validé par le responsable</h4>
                    <p class="text-muted m-0 small">Votre affectation à l'entreprise <strong><?= htmlspecialchars($affichage_entreprise) ?></strong> est officiellement approuvée.</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 pt-3 border-top">
            <h3 class="fw-bold m-0 text-secondary" style="font-size: 1.5rem;"><i class="bi bi-file-earmark-text me-2"></i>Formulaire de Convention</h3>
            
            <?php 
            $statut_signature = strtolower(trim($stage['convention_signee'] ?? 'non'));
            if ($statut_signature === 'oui' || $stage['convention_signee'] == 1): ?>
                <span class="badge bg-success px-3 py-2 rounded-3">✓ Signée & Validée</span>
            <?php elseif (!empty($convention_data)): ?>
                <span class="badge bg-warning text-dark px-3 py-2 rounded-3">⏳ En attente de signature</span>
            <?php else: ?>
                <span class="badge bg-secondary px-3 py-2 rounded-3">📋 À compléter</span>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'saved'): ?>
            <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3">✨ Vos informations de convention ont été transmises avec succès au responsable !</div>
        <?php endif; ?>

        <?php if ($statut_signature === 'oui' || $stage['convention_signee'] == 1): ?>
            <div class="card border-0 p-5 text-center bg-light shadow-sm mb-5" style="border-radius: 20px;">
                <i class="bi bi-file-earmark-check text-success mb-3" style="font-size: 3.5rem;"></i>
                <h5 class="fw-bold text-dark">Votre convention PDF officielle est disponible</h5>
                <p class="text-muted mb-4">Le document complet a été généré et signé numériquement par le Directeur de l'IUT.</p>
                <div>
                    <a href="generer_pdf.php" class="btn btn-success fw-bold px-4 py-2.5 shadow-sm" style="border-radius: 10px; background-color: #198754 !important; border:none;">
                        <i class="bi bi-download me-2"></i> Télécharger ma Convention (PDF)
                    </a>
                </div>
            </div>

        <?php elseif (!empty($convention_data)): ?>
            <div class="card border-0 p-5 text-center bg-light shadow-sm mb-5" style="border-radius: 20px;">
                <i class="bi bi-hourglass-split text-warning mb-3" style="font-size: 3rem;"></i>
                <h5 class="fw-bold text-dark">Données envoyées au secrétariat</h5>
                <p class="text-muted m-0">Le responsable des stages procède aux vérifications administratives nécessaires pour valider et apposer sa signature.</p>
            </div>

        <?php else: ?>
            <div class="card border-0 shadow-sm p-4 bg-white mb-5" style="border-radius: 20px; border-top: 4px solid #0066FF;">
                <p class="text-muted small mb-4">Veuillez compléter les informations obligatoires de votre convention ci-dessous pour lancer la procédure d'édition du PDF juridique.</p>
                
                <form action="save_convention.php" method="POST">
                    <input type="hidden" name="id_stage" value="<?= $stage['id_stage'] ?>">

                    <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-person me-2"></i>1. Informations complémentaires du Stagiaire</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Nom & Prénom</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($stage['prenom_etud'] ?? '') ?> <?= strtoupper(htmlspecialchars($stage['nom_etud'] ?? '')) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Numéro d'étudiant</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($stage['num_etudiant'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Téléphone portable *</label>
                            <input type="tel" name="telephone_etudiant" class="form-control" placeholder="Ex: 0612345678" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">Date de naissance *</label>
                            <input type="date" name="date_naissance" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">Lieu de naissance *</label>
                            <input type="text" name="lieu_naissance" class="form-control" placeholder="Ville et (N° Dépt)" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary">Adresse postale personnelle complète *</label>
                            <input type="text" name="adresse_etudiant" class="form-control" placeholder="N°, nom de rue, code postal et ville" required>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-briefcase me-2"></i>2. Informations administratives de l'Entreprise</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Raison sociale</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($affichage_entreprise) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Numéro SIRET de l'établissement *</label>
                            <input type="text" name="siret" class="form-control" placeholder="14 chiffres obligatoires" maxlength="14" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Représentant légal de l'organisme *</label>
                            <input type="text" name="representant_legal" class="form-control" placeholder="Nom, Prénom et Fonction (Ex: Sophie MARTIN - Directrice RH)" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Maître de stage</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars(($stage['prenom_maitre'] ?? '').' '.($stage['nom_maitre'] ?? 'Non assigné')) ?>" readonly>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-journal-text me-2"></i>3. Aménagement des temps et objectifs pédagogiques</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">Date de début</label>
                            <input type="text" class="form-control bg-light" value="<?= !empty($date_debut_formatee) ? $date_debut_formatee : 'Non spécifiée' ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">Date de fin</label>
                            <input type="text" class="form-control bg-light" value="<?= !empty($date_fin_formatee) ? $date_fin_formatee : 'Non spécifiée' ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">Volume horaire total *</label>
                            <input type="number" name="heures_totales" class="form-control" placeholder="Ex: 280" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">Modalités de présence *</label>
                            <select name="modalite_presence" class="form-select" required>
                                <option value="Sur site">100% Présentiel</option>
                                <option value="Télétravail">100% Télétravail</option>
                                <option value="Hybride">Mixte (Hybride)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Service interne d'accueil *</label>
                            <input type="text" name="service_affectation" class="form-control" placeholder="Ex: Pôle Créatif / Équipe UX-UI" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Horaires de travail hebdomadaires *</label>
                            <input type="text" name="horaires_travail" class="form-control" placeholder="Ex: 35h/semaine (9h-12h30 / 14h-17h35)" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary">Missions détaillées & Objectifs assignés *</label>
                            <textarea name="objectifs_pedagogiques" class="form-control" rows="4" placeholder="Décrivez de manière précise vos futures tâches au sein de l'entreprise..." required></textarea>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-cash-coin me-2"></i>4. Gratification financière</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Montant de la gratification (Indiquer 0 si non gratifié) *</label>
                            <input type="text" name="montant_gratification" class="form-control" placeholder="Ex: 4.35 € / heure" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Modalités de paiement</label>
                            <input type="text" name="modalite_versement" class="form-control" placeholder="Ex: Virement bancaire en fin de mois">
                        </div>
                    </div>

                    <div class="text-end pt-2">
                        <button type="submit" class="btn btn-primary fw-bold px-5 py-2.5 shadow-sm" style="background-color: #0066FF !important; border: none; border-radius: 10px;">
                            <i class="bi bi-send-check me-2"></i> Valider et envoyer ma Convention
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <div class="card border-0 bg-light p-4 shadow-sm" style="border-radius: 15px;">
            <h6 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Un problème avec votre stage ?</h6>
            <form method="POST">
                <div class="input-group">
                    <input type="text" name="message" class="form-control shadow-none border-0" placeholder="Signaler un incident au responsable..." required>
                    <button type="submit" name="send_incident" class="btn btn-danger px-4 fw-bold">Signaler</button>
                </div>
            </form>
            <?php if(isset($success_msg)): ?><small class="text-success fw-bold d-block mt-2"><?= $success_msg ?></small><?php endif; ?>
        </div>

    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>