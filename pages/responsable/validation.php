<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : On autorise le Responsable OU l'Admin
if ($_SESSION['role'] !== 'Responsable stage' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// LOGIQUE DE VALIDATION ET MISE À JOUR DES DONNÉES
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_recherche = $_GET['id'];
    
    if ($_GET['action'] == 'valider') {
        try {
            $pdo->beginTransaction();

            $query = "SELECT r.*, ef.num_etudiant 
                      FROM Recherche r 
                      JOIN Effectuer ef ON r.id_recherche = ef.id_recherche 
                      WHERE r.id_recherche = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$id_recherche]);
            $data = $stmt->fetch();

            if ($data) {
                $checkStage = $pdo->prepare("SELECT id_stage FROM Stage WHERE num_etudiant = ?");
                $checkStage->execute([$data['num_etudiant']]);
                
                if (!$checkStage->fetch()) {
                    $insertStage = $pdo->prepare("INSERT INTO Stage (num_etudiant, lieu, convention_signee) VALUES (?, ?, 'non')");
                    $insertStage->execute([
                        $data['num_etudiant'], 
                        $data['entreprise_contactee']
                    ]);
                }

                $updateRecherche = $pdo->prepare("UPDATE Recherche SET statut = 'Validée' WHERE id_recherche = ?");
                $updateRecherche->execute([$id_recherche]);

                $pdo->commit();
                header("Location: validation.php?status=validated");
                exit();
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<div class='alert alert-danger m-4 border-0 shadow-sm' style='border-radius: 12px;'>Erreur lors de la validation : " . $e->getMessage() . "</div>";
        }
    
    } elseif ($_GET['action'] == 'refuser') {
        $stmt = $pdo->prepare("UPDATE Recherche SET statut = 'Refusé' WHERE id_recherche = ?");
        $stmt->execute([$id_recherche]);
        header("Location: validation.php");
        exit();
    }
    
    // LOGIQUE DE SIGNATURE DE LA CONVENTION
    elseif ($_GET['action'] == 'signer_convention') {
        try {
            $queryEtud = "SELECT ef.num_etudiant FROM Recherche r 
                          JOIN Effectuer ef ON r.id_recherche = ef.id_recherche 
                          WHERE r.id_recherche = ?";
            $stmtEtud = $pdo->prepare($queryEtud);
            $stmtEtud->execute([$id_recherche]);
            $etud = $stmtEtud->fetch();

            if ($etud) {
                $stmtSigner = $pdo->prepare("UPDATE Stage SET convention_signee = 'oui' WHERE num_etudiant = ?");
                $stmtSigner->execute([$etud['num_etudiant']]);
            }
            
            header("Location: validation.php?status=signed");
            exit();
        } catch (PDOException $e) {
            die("Erreur lors de la signature : " . $e->getMessage());
        }
    }
}

// ATTENTION MODIFICATION ICI : On récupère les dossiers 'En attente' OU ceux 'Validée' mais dont la convention n'est pas encore signée
$recherches = $pdo->query("SELECT r.*, e.nom, e.prenom, e.num_etudiant 
                           FROM Recherche r 
                           JOIN Effectuer ef ON r.id_recherche = ef.id_recherche 
                           JOIN Etudiant e ON ef.num_etudiant = e.num_etudiant 
                           LEFT JOIN Stage s ON e.num_etudiant = s.num_etudiant
                           WHERE r.statut = 'En attente' OR (r.statut = 'Validée' AND (s.convention_signee = 'non' OR s.convention_signee IS NULL))
                           ORDER BY r.date_recherche ASC")->fetchAll();
?>

<link rel="stylesheet" href="../../assets/css/style.css">

<div class="container py-5" style="max-width: 1140px;">
    <h1 class="fw-bold mb-5 d-flex align-items-center gap-3" style="color: #000000; font-size: 2.2rem; letter-spacing: -0.5px;">
        <i class="bi bi-check2-square text-dark"></i> Dossiers de stage et Conventions
    </h1>
    
    <?php if (isset($_GET['status']) && $_GET['status'] == 'signed'): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">✍️ La convention a été signée numériquement avec succès ! L'étudiant peut maintenant la télécharger.</div>
    <?php endif; ?>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'validated'): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">✅ Le stage a été validé ! L'étudiant peut maintenant remplir son formulaire de convention.</div>
    <?php endif; ?>
    
    <?php if(empty($recherches)): ?>
        <div class="bg-white p-5 text-center border-0 shadow-sm" style="border-radius: 24px;">
            <i class="bi bi-check2-all display-4 text-success mb-3 d-block"></i>
            <h5 class="fw-bold text-dark mb-1">Tout est à jour !</h5>
            <p class="text-muted mb-0 small">Tous les dossiers et conventions ont été traités.</p>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-4">
            <?php foreach($recherches as $r): 
                // --- 1. CHARGEMENT ET DÉCODAGE DE LA CONVENTION JSON ---
                $stmtStage = $pdo->prepare("SELECT description, convention_signee FROM Stage WHERE num_etudiant = ?");
                $stmtStage->execute([$r['num_etudiant']]);
                $stageInfo = $stmtStage->fetch();

                $convention_data = [];
                $statut_convention = "Aucun formulaire";
                if ($stageInfo && !empty($stageInfo['description'])) {
                    $decoded = json_decode($stageInfo['description'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $convention_data = $decoded;
                        $statut_convention = (strtolower(trim($stageInfo['convention_signee'])) === 'oui') ? "Signée" : "En attente";
                    }
                }

                // --- 2. EXTRACTION DU MAÎTRE DE STAGE SÉCURISÉE ---
                $lignes = explode("\n", $r['reponses']);
                $tuteur_nom = ""; $tuteur_prenom = ""; $tuteur_mail = "";
                foreach($lignes as $l) {
                    $l = trim($l);
                    if (strpos($l, ':') !== false) {
                        list($cle, $valeur) = explode(':', $l, 2);
                        $cle = trim($cle); $valeur = trim($valeur);
                        if (strcasecmp($cle, 'NOM') === 0) { $tuteur_nom = $valeur; }
                        elseif (strcasecmp($cle, 'PRÉNOM') === 0 || strcasecmp($cle, 'PRENOM') === 0) { $tuteur_prenom = $valeur; }
                        elseif (strcasecmp($cle, 'EMAIL') === 0 || strcasecmp($cle, 'MAIL') === 0) { $tuteur_mail = $valeur; }
                    }
                }
                $maitre_complet = trim($tuteur_prenom . " " . $tuteur_nom);
                if(empty($maitre_complet)) { $maitre_complet = "Non renseigné"; }
            ?>
            <div class="bg-white p-4 border-0 shadow-sm" style="border-radius: 20px;">
                
                <div class="d-flex justify-content-between align-items-start flex-wrap flex-lg-nowrap gap-4">
                    <div style="flex: 1.1; min-width: 240px;">
                        <h3 class="fw-bold text-primary mb-1" style="color: #0066FF !important; font-size: 1.5rem; letter-spacing: -0.3px;">
                            <?= htmlspecialchars($r['prenom']) ?> <?= strtoupper($r['nom']) ?>
                        </h3>
                        <div class="text-muted mb-3 italic small" style="font-size: 0.8rem;">
                            Soumis le <?= date('d/m/Y', strtotime($r['date_recherche'])) ?> 
                            <?php if($r['statut'] == 'Validée'): ?>
                                <span class="badge bg-success-subtle text-success px-2 py-1 ms-2">Stage Validé</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning px-2 py-1 ms-2">Recherche en attente</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2 text-dark small fw-bold mb-1">
                            <i class="bi bi-building text-secondary"></i> Entreprise :
                        </div>
                        <div class="text-secondary small ps-4">
                            <?= htmlspecialchars($r['entreprise_contactee']) ?>
                        </div>
                    </div>

                    <div style="flex: 1; min-width: 220px;">
                        <div class="d-flex align-items-center gap-2 text-dark small fw-bold mb-2">
                            <i class="bi bi-person-badge-fill text-secondary"></i> Maître de Stage :
                        </div>
                        <div class="fw-bold text-primary ps-4 small mb-1" style="color: #0066FF !important;">
                            <?= htmlspecialchars($maitre_complet) ?>
                        </div>
                        <?php if(!empty($tuteur_mail)): ?>
                            <div class="text-muted ps-4 small" style="font-size: 0.8rem;"><?= htmlspecialchars($tuteur_mail) ?></div>
                        <?php endif; ?>
                    </div>

                    <div style="flex: 1.5; min-width: 300px;">
                        <div class="d-flex align-items-center gap-2 text-dark small fw-bold mb-2">
                            <i class="bi bi-file-earmark-text-fill text-secondary"></i> Sujet : <span class="fw-medium text-secondary"><?= htmlspecialchars($r['offre_consultee']) ?></span>
                        </div>
                        
                        <div class="p-3 border-0 rounded-3 text-secondary" style="background-color: #F8FAFC; font-size: 0.85rem;">
                            <span class="fw-bold text-dark d-block mb-1">Détails recherche :</span>
                            <div style="max-height: 90px; overflow-y: auto; line-height: 1.4;">
                                <?= !empty($r['reponses']) ? nl2br(htmlspecialchars($r['reponses'])) : "<em>Aucun détail supplémentaire</em>" ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 text-end ps-lg-3" style="min-width: 180px; align-self: center;">
                        
                        <?php if($r['statut'] !== 'Validée'): ?>
                            <a href="validation.php?action=valider&id=<?= $r['id_recherche'] ?>" 
                               class="btn text-white fw-bold py-2 px-3 text-center border-0 small" 
                               style="background-color: #76BA99; border-radius: 6px; font-size: 0.85rem;">
                                Valider le Stage
                            </a>
                            <a href="validation.php?action=refuser&id=<?= $r['id_recherche'] ?>" 
                               class="btn btn-outline-danger fw-bold py-2 px-3 text-center bg-white small" 
                               style="border-radius: 6px; font-size: 0.85rem; border-color: #E07A7A; color: #E07A7A;">
                                Refuser
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($convention_data)): ?>
                            <a href="validation.php?action=signer_convention&id=<?= $r['id_recherche'] ?>" 
                               class="btn btn-primary fw-bold py-2 px-3 text-center small shadow-sm" 
                               style="border-radius: 6px; font-size: 0.85rem; background-color: #0066FF !important; border: none;"
                               onclick="return confirm('Êtes-vous sûr de vouloir signer numériquement cette convention ?');">
                                <i class="bi bi-pencil-fill small me-1"></i> Signer la Convention
                            </a>
                        <?php else: ?>
                            <button class="btn btn-light text-muted small py-2 px-3" style="font-size: 0.8rem;" disabled>
                                ⏳ Convention non remplie
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($convention_data)): ?>
                    <div class="mt-4 p-3 rounded-3 border-start border-3 border-warning shadow-sm" style="background-color: #FFFDF9;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-dark"><i class="bi bi-file-earmark-text-fill text-warning me-2"></i> Formulaire de Convention reçu :</span>
                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small">⏳ En attente de signature administrative</span>
                        </div>
                        
                        <div class="row g-3 text-secondary small" style="font-size: 0.88rem;">
                            <div class="col-md-4"><strong>Téléphone étudiant :</strong> <span class="text-dark"><?= htmlspecialchars($convention_data['telephone_etudiant']) ?></span></div>
                            <div class="col-md-4"><strong>Date & Lieu de Naissance :</strong> <span class="text-dark"><?= htmlspecialchars($convention_data['date_naissance']) ?> à <?= htmlspecialchars($convention_data['lieu_naissance']) ?></span></div>
                            <div class="col-md-4"><strong>SIRET :</strong> <span class="text-dark"><?= htmlspecialchars($convention_data['siret']) ?></span></div>
                            <div class="col-md-6"><strong>Adresse Étudiant :</strong> <span class="text-dark"><?= htmlspecialchars($convention_data['adresse_etudiant']) ?></span></div>
                            <div class="col-md-6"><strong>Représentant Légal (Signataire) :</strong> <span class="text-dark"><?= htmlspecialchars($convention_data['representant_legal']) ?></span></div>
                            <div class="col-md-4"><strong>Volume horaire :</strong> <span class="text-dark"><?= htmlspecialchars($convention_data['heures_totales']) ?>h (<?= htmlspecialchars($convention_data['modalite_presence']) ?>)</span></div>
                            <div class="col-md-4"><strong>Service d'accueil :</strong> <span class="text-dark"><?= htmlspecialchars($convention_data['service_affectation']) ?></span></div>
                            <div class="col-md-4"><strong>Gratification :</strong> <span class="text-dark"><?= htmlspecialchars($convention_data['montant_gratification']) ?></span></div>
                            <?php if(!empty($convention_data['modalite_versement'])): ?>
                                <div class="col-12 text-muted"><strong>Modalités de versement :</strong> <span class="text-dark"><?= htmlspecialchars($convention_data['modalite_versement']) ?></span></div>
                            <?php endif; ?>
                            <div class="col-12 mt-2 bg-white p-3 rounded border">
                                <strong>Missions détaillées juridiques :</strong>
                                <p class="text-dark m-0 mt-1" style="white-space: pre-line;"><?= htmlspecialchars($convention_data['objectifs_pedagogiques']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>