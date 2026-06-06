<?php
session_start();
require_once '../../includes/db.php';

// Sécurité : Vérification de la connexion de l'étudiant
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit();
}
$id_etud = $_SESSION['user_id'];

// Récupération des informations du stage et de l'étudiant
$query = "SELECT s.*, e.Nom AS nom_etud, e.Prenom AS prenom_etud, e.promotion, ent.Nom AS nom_ent, ent.Adresse AS adresse_ent, m.Nom AS nom_maitre, m.Prenom AS prenom_maitre
          FROM Stage s
          JOIN etudiant e ON s.num_etudiant = e.num_etudiant
          LEFT JOIN Entreprise ent ON s.id_ent = ent.id_ent
          LEFT JOIN Maitre_stage m ON s.id_maitre = m.id_maitre
          WHERE s.num_etudiant = ? LIMIT 1";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_etud]);
$stage = $stmt->fetch(PDO::FETCH_ASSOC);

// Sécurité : On vérifie que la convention est bien validée et signée
if (!$stage || (strtolower(trim($stage['convention_signee'] ?? '')) !== 'oui' && $stage['convention_signee'] != 1)) {
    die("Erreur : Votre convention n'a pas encore été signée par l'administration.");
}

// Décoder les données juridiques de la convention
$convention_data = json_decode($stage['description'] ?? '{}', true);

// --- SÉCURISATION ET FORMATAGE DES DATES (Évite l'erreur Deprecated et 01/01/1970) ---
$date_debut_formatee = "Non spécifiée";
$date_fin_formatee = "Non spécifiée";

if ($stage) {
    // Vérification date de début (casse Majuscule puis minuscule)
    if (!empty($stage['Date_debut'])) {
        $date_debut_formatee = date('d/m/Y', strtotime($stage['Date_debut']));
    } elseif (!empty($stage['date_debut'])) {
        $date_debut_formatee = date('d/m/Y', strtotime($stage['date_debut']));
    }
    
    // Vérification date de fin (casse Majuscule puis minuscule)
    if (!empty($stage['Date_fin'])) {
        $date_fin_formatee = date('d/m/Y', strtotime($stage['Date_fin']));
    } elseif (!empty($stage['date_fin'])) {
        $date_fin_formatee = date('d/m/Y', strtotime($stage['date_fin']));
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Convention_Stage_<?= htmlspecialchars($stage['nom_etud']) ?>.pdf</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Helvetica Neue', Arial, sans-serif; color: #333; }
        .page-convention { background: white; max-width: 800px; margin: 30px auto; padding: 50px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-radius: 8px; position: relative; }
        .header-iut { border-bottom: 2px solid #0066FF; padding-bottom: 20px; margin-bottom: 30px; }
        .titre-principal { color: #0066FF; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .section-title { background-color: #f0f4f8; color: #1e293b; font-weight: 700; padding: 8px 15px; border-left: 4px solid #0066FF; margin-top: 25px; margin-bottom: 15px; font-size: 1.05rem; }
        .signature-box { border: 1px dashed #cbd5e1; background: #fafafa; padding: 20px; border-radius: 6px; text-align: center; height: 160px; display: flex; flex-direction: column; justify-content: space-between; }
        .badge-signature { background-color: #e2e8f0; color: #475569; font-weight: bold; font-size: 0.75rem; padding: 4px 8px; border-radius: 4px; display: inline-block; margin-top: 10px; }
        @media print {
            body { background: white; }
            .page-convention { box-shadow: none; margin: 0; padding: 0; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="container text-center mt-4 no-print">
    <button onclick="window.print();" class="btn btn-success fw-bold px-4 py-2 shadow-sm">
        <i class="bi bi-printer-fill me-2"></i> Imprimer ou Enregistrer en PDF
    </button>
    <a href="mon_stage.php" class="btn btn-outline-secondary fw-bold px-4 py-2 ms-2 shadow-sm">Retour au profil</a>
</div>

<div class="page-convention">
    <div class="header-iut d-flex justify-content-between align-items-center">
        <div>
            <h4 class="m-0 fw-bold text-dark">INSTITUT UNIVERSITAIRE DE TECHNOLOGIE</h4>
            <p class="text-muted m-0 small">Service des Stages & Relations Entreprises</p>
            <p class="text-muted m-0 small" style="font-size: 0.75rem;">Filière : MMI</p>
        </div>
        <div class="text-end">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded">CONVENTION SÉCURISÉE</span>
        </div>
    </div>

    <div class="text-center my-4">
        <h2 class="titre-principal">Convention de Stage Professionnelle</h2>
        <p class="text-muted">Établie conformément aux dispositions du code de l'éducation nationale</p>
    </div>

    <div class="section-title">ARTICLE 1 : LE STAGIAIRE (ÉTUDIANT)</div>
    <div class="row g-2 ps-2">
        <div class="col-6"><strong>Nom & Prénom :</strong> <?= htmlspecialchars($stage['prenom_etud'] ?? '') ?> <?= strtoupper(htmlspecialchars($stage['nom_etud'] ?? '')) ?></div>
        <div class="col-6"><strong>N° Étudiant :</strong> <?= htmlspecialchars($stage['num_etudiant'] ?? '') ?></div>
        <div class="col-6"><strong>Date de naissance :</strong> <?= htmlspecialchars($convention_data['date_naissance'] ?? 'Non renseignée') ?></div>
        <div class="col-6"><strong>Lieu de naissance :</strong> <?= htmlspecialchars($convention_data['lieu_naissance'] ?? 'Non renseigné') ?></div>
        <div class="col-6"><strong>Téléphone :</strong> <?= htmlspecialchars($convention_data['telephone_etudiant'] ?? 'Non renseigné') ?></div>
        <div class="col-6"><strong>Promotion / Classe :</strong> <?= htmlspecialchars($stage['promotion'] ?? 'IUT 2ème Année') ?></div>
        <div class="col-12"><strong>Adresse légale :</strong> <?= htmlspecialchars($convention_data['adresse_etudiant'] ?? 'Non renseignée') ?></div>
    </div>

    <div class="section-title">ARTICLE 2 : L'ORGANISME D'ACCUEIL (ENTREPRISE)</div>
    <div class="row g-2 ps-2">
        <div class="col-6"><strong>Raison Sociale :</strong> <?= htmlspecialchars($stage['nom_ent'] ?? ($stage['lieu'] ?? 'Non spécifiée')) ?></div>
        <div class="col-6"><strong>Numéro SIRET :</strong> <?= htmlspecialchars($convention_data['siret'] ?? 'Non renseigné') ?></div>
        <div class="col-12"><strong>Adresse Siège Social :</strong> <?= htmlspecialchars($stage['adresse_ent'] ?? 'Adresse enregistrée en base') ?></div>
        <div class="col-6"><strong>Représentant Légal :</strong> <?= htmlspecialchars($convention_data['representant_legal'] ?? 'Non renseigné') ?></div>
        <div class="col-6"><strong>Maître de Stage :</strong> <?= htmlspecialchars($stage['prenom_maitre'] ?? '') ?> <?= strtoupper(htmlspecialchars($stage['nom_maitre'] ?? 'Non assigné')) ?></div>
    </div>

    <div class="section-title">ARTICLE 3 : AMÉNAGEMENT DU TEMPS DE TRAVAIL</div>
    <div class="row g-2 ps-2">
        <div class="col-6"><strong>Date de Début :</strong> <?= $date_debut_formatee ?></div>
        <div class="col-6"><strong>Date de Fin :</strong> <?= $date_fin_formatee ?></div>
        <div class="col-6"><strong>Volume Horaire Total :</strong> <?= htmlspecialchars($convention_data['heures_totales'] ?? '280') ?> Heures</div>
        <div class="col-6"><strong>Modalité de présence :</strong> <?= htmlspecialchars($convention_data['modalite_presence'] ?? 'Sur site') ?></div>
        <div class="col-6"><strong>Service d'accueil :</strong> <?= htmlspecialchars($convention_data['service_affectation'] ?? 'Pôle Technique') ?></div>
        <div class="col-6"><strong>Horaires hebdomadaires :</strong> <?= htmlspecialchars($convention_data['horaires_travail'] ?? '35h') ?></div>
    </div>

    <div class="section-title">ARTICLE 4 : MISSIONS ET OBJECTIFS PÉDAGOGIQUES</div>
    <div class="p-3 bg-light rounded border small" style="white-space: pre-line; line-height: 1.5;">
        <?= htmlspecialchars($convention_data['objectifs_pedagogiques'] ?? 'Aucune mission détaillée saisie.') ?>
    </div>

    <div class="section-title">ARTICLE 5 : GRATIFICATION ET VERSEMENT</div>
    <div class="row g-2 ps-2">
        <div class="col-6"><strong>Montant de l'indemnité :</strong> <?= htmlspecialchars($convention_data['montant_gratification'] ?? '0 €') ?></div>
        <div class="col-6"><strong>Mode de Versement :</strong> <?= htmlspecialchars($convention_data['modalite_versement'] ?? 'Virement bancaire') ?></div>
    </div>

    <div class="row g-3 mt-5">
        <div class="col-4">
            <div class="signature-box">
                <span class="fw-bold small text-muted">Signature de l'Étudiant</span>
                <div class="text-success small fw-bold">✓ Lu et approuvé</div>
            </div>
        </div>
        <div class="col-4">
            <div class="signature-box">
                <span class="fw-bold small text-muted">Signature Entreprise</span>
                <div class="text-success small fw-bold">✓ Bon pour accord</div>
            </div>
        </div>
        <div class="col-4">
            <div class="signature-box border-success" style="background-color: #f4fbf7;">
                <span class="fw-bold small text-success">Le Directeur de l'IUT</span>
                <div>
                    <span class="badge-signature bg-success text-white">✓ SIGNÉ NUMÉRIQUEMENT</span>
                    <div class="text-muted extra-small mt-1" style="font-size: 0.65rem;">ID-Authentification :<br>SEC-<?= md5($stage['id_stage'] ?? 'default') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>