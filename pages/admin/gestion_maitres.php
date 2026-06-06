<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : Admin uniquement
if ($_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// Récupération de tous les stages validés qui ont des infos de maître de stage
$sql = "SELECT r.entreprise_contactee, r.reponses, e.nom as etud_nom, e.prenom as etud_prenom 
        FROM recherche r 
        JOIN effectuer ef ON r.id_recherche = ef.id_recherche 
        JOIN etudiant e ON ef.num_etudiant = e.num_etudiant
        WHERE r.statut = 'Validée' AND r.reponses IS NOT NULL AND r.reponses != '0'
        ORDER BY r.entreprise_contactee ASC";

$stmt = $pdo->query($sql);
$maitres = $stmt->fetchAll();
?>

<link rel="stylesheet" href="../../assets/css/style.css">
<div class="container py-5" style="max-width: 1000px;">
    
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <h1 class="fw-bold m-0 d-flex align-items-center gap-3" style="color: #000000; font-size: 2.2rem; letter-spacing: -0.5px;">
            <i class="bi bi-people-fill text-primary" style="color: #0d6efd !important;"></i> Gestion des Maîtres de Stage
        </h1>
        <span class="badge px-3 py-2 fw-medium" style="background-color: #007bff; color: #FFFFFF; border-radius: 20px; font-size: 0.9rem;">
            <?= count($maitres) ?> Tuteur<?= count($maitres) > 1 ? 's' : '' ?> enregistré<?= count($maitres) > 1 ? 's' : '' ?>
        </span>
    </div>

    <div class="d-flex flex-column gap-4">
        <?php if (empty($maitres)): ?>
            <div class="bg-white p-5 text-center border-0" style="border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.05);">
                <span class="text-muted">Aucun maître de stage trouvé dans les dossiers validés.</span>
            </div>
        <?php else: ?>
            <?php foreach ($maitres as $m): 
                // Extraction des infos du texte brut (reponses)
                $infos = explode("\n", $m['reponses']);
                $nom_tuteur = "";
                $prenom_tuteur = "";
                $email_tuteur = "Pas d'email";
                
                foreach($infos as $ligne) {
                    // On nettoie les espaces invisibles en début et fin de ligne
                    $ligne = trim($ligne);
                    
                    // Si la ligne contient un deux-points, on la découpe proprement
                    if (strpos($ligne, ':') !== false) {
                        list($cle, $valeur) = explode(':', $ligne, 2);
                        $cle = trim($cle);
                        $valeur = trim($valeur);
                        
                        // On attribue les variables selon la clé trouvée
                        if ($cle === 'NOM') {
                            $nom_tuteur = $valeur;
                        } elseif ($cle === 'PRÉNOM' || $cle === 'PRENOM') {
                            $prenom_tuteur = $valeur;
                        } elseif ($cle === 'EMAIL') {
                            $email_tuteur = $valeur;
                        }
                    }
                }
                
                // On assemble le nom complet proprement (ex: DUPONT Jean)
                $nom_complet = trim($nom_tuteur . " " . $prenom_tuteur);
                if (empty($nom_complet)) {
                    $nom_complet = "Non spécifié";
                }
            ?>
                <div class="bg-white p-4 border-0 d-flex align-items-center justify-content-between flex-wrap gap-4 item-carte" 
                    style="border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.04), 0 3px 10px rgba(0,0,0,0.015) !important; transition: transform 0.2s ease;">
                    
                    <div style="min-width: 220px;">
                        <h4 class="fw-bold m-0 text-dark" style="font-size: 1.25rem; letter-spacing: -0.3px;"><?= htmlspecialchars($nom_complet) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.88rem;"><?= htmlspecialchars($email_tuteur) ?></div>
                    </div>

                    <div class="text-start text-md-center" style="min-width: 150px;">
                        <span class="fw-bold text-dark" style="font-size: 1.2rem;"><?= htmlspecialchars($m['entreprise_contactee']) ?></span>
                    </div>

                    <div style="min-width: 250px;">
                        <span class="text-secondary fw-medium">Étudiant encadré : </span>
                        <strong class="text-dark"><?= strtoupper(htmlspecialchars($m['etud_nom'])) ?> <?= htmlspecialchars($m['etud_prenom']) ?></strong>
                    </div>

                    <div>
                        <button class="btn fw-medium d-inline-flex align-items-center gap-2 px-3 text-white shadow-sm" 
                                style="background-color: #2F448A; border-radius: 8px; font-size: 0.85rem; height: 38px; border: none;"
                                onclick="alert('Ce tuteur n\'a pas encore de compte utilisateur.')">
                            <i class="bi bi-check2-circle"></i> MODIFIER PW
                        </button>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .item-carte:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 40px rgba(0,0,0,0.06) !important;
    }
</style>

<?php include '../../includes/footer.php'; ?>