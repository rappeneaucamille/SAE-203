<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : On autorise le Responsable OU l'Admin
if ($_SESSION['role'] !== 'Responsable stage' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// LOGIQUE DE VALIDATION ET MISE À JOUR DES DONNÉES (STRICTEMENT IDENTIQUE)
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
                echo "<div class='alert alert-success m-4 border-0 shadow-sm' style='border-radius: 12px;'>✅ Stage validé avec succès !</div>";
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<div class='alert alert-danger m-4 border-0 shadow-sm' style='border-radius: 12px;'>Erreur lors de la validation : " . $e->getMessage() . "</div>";
        }
    } 
    elseif ($_GET['action'] == 'refuser') {
        $pdo->prepare("UPDATE Recherche SET statut = 'Refusé' WHERE id_recherche = ?")->execute([$id_recherche]);
        echo "<div class='alert alert-warning m-4 border-0 shadow-sm' style='border-radius: 12px;'>Dossier refusé.</div>";
    }
    elseif ($_GET['action'] == 'signer_convention') {
        try {
            $stmtEtud = $pdo->prepare("SELECT num_etudiant FROM Effectuer WHERE id_recherche = ?");
            $stmtEtud->execute([$id_recherche]);
            $liaison = $stmtEtud->fetch();
            
            if ($liaison) {
                $updateConv = $pdo->prepare("UPDATE Stage SET convention_signee = 'oui' WHERE num_etudiant = ?");
                $updateConv->execute([$liaison['num_etudiant']]);
                echo "<div class='alert alert-success m-4 border-0 shadow-sm' style='border-radius: 12px;'>✍️ Convention marquée comme signée avec succès !</div>";
            }
        } catch (Exception $e) {
            $pdo->get_html_theme_color = "danger";
            echo "<div class='alert alert-danger m-4 border-0 shadow-sm' style='border-radius: 12px;'>Erreur lors de la signature : " . $e->getMessage() . "</div>";
        }
    }
}

// Affichage des dossiers en attente
$recherches = $pdo->query("SELECT r.*, e.nom, e.prenom FROM Recherche r JOIN Effectuer ef ON r.id_recherche = ef.id_recherche JOIN Etudiant e ON ef.num_etudiant = e.num_etudiant WHERE r.statut = 'En attente' ORDER BY r.date_recherche ASC")->fetchAll();
?>

<div class="container py-5" style="max-width: 1140px;">
    <h1 class="fw-bold mb-5 d-flex align-items-center gap-3" style="color: #000000; font-size: 2.2rem; letter-spacing: -0.5px;">
        <i class="bi bi-check2-square text-dark"></i> Dossiers de stage à valider
    </h1>
    
    <?php if(empty($recherches)): ?>
        <div class="bg-white p-5 text-center border-0 shadow-sm" style="border-radius: 24px;">
            <i class="bi bi-check2-all display-4 text-success mb-3 d-block"></i>
            <h5 class="fw-bold text-dark mb-1">Tout est à jour !</h5>
            <p class="text-muted mb-0 small">Tous les dossiers d'étudiants ont été traités.</p>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-4">
            <?php foreach($recherches as $r): 
                // --- EXTRACTION DU MAÎTRE DE STAGE ---
                $lignes = explode("\n", $r['reponses']);
                $tuteur_nom = "";
                $tuteur_prenom = "";
                $tuteur_mail = "";
                
                foreach($lignes as $l) {
                    if(stripos($l, 'NOM :') !== false) {
                        $tuteur_nom = trim(str_ireplace('NOM :', '', $l));
                    }
                    if(stripos($l, 'PRÉNOM :') !== false) {
                        $tuteur_prenom = trim(str_ireplace('PRÉNOM :', '', $l));
                    }
                    if(stripos($l, 'EMAIL :') !== false || stripos($l, 'MAIL :') !== false) {
                        $tuteur_mail = trim(str_ireplace(['EMAIL :', 'MAIL :'], '', $l));
                    }
                }
                $maitre_complet = trim($tuteur_prenom . " " . $tuteur_nom);
                if(empty($maitre_complet)) $maitre_complet = "Non renseigné";
            ?>
            <div class="bg-white p-4 border-0 shadow-sm d-flex justify-content-between align-items-start flex-wrap flex-lg-nowrap gap-4" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04), 0 2px 10px rgba(0,0,0,0.01) !important;">
                
                <div style="flex: 1.1; min-width: 240px;">
                    <h3 class="fw-bold text-primary mb-1" style="color: #0066FF !important; font-size: 1.5rem; letter-spacing: -0.3px;">
                        <?= htmlspecialchars($r['prenom']) ?> <?= strtoupper($r['nom']) ?>
                    </h3>
                    <div class="text-muted mb-3 italic small" style="font-size: 0.8rem;">
                        Soumis le <?= date('d/m/Y', strtotime($r['date_recherche'])) ?>
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
                        <span class="fw-bold text-dark d-block mb-1">Détails :</span>
                        <div style="max-height: 90px; overflow-y: auto; line-height: 1.4;">
                            <?= !empty($r['reponses']) ? nl2br(htmlspecialchars($r['reponses'])) : "<em>Aucun détail supplémentaire</em>" ?>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 text-end ps-lg-3" style="min-width: 180px; align-self: center;">
                    
                    <a href="validation.php?action=valider&id=<?= $r['id_recherche'] ?>" 
                       class="btn text-white fw-bold py-2 px-3 text-center border-0 small" 
                       style="background-color: #76BA99; border-radius: 6px; font-size: 0.85rem; letter-spacing: 0.3px;">
                        Valider
                    </a>
                    
                    <a href="validation.php?action=signer_convention&id=<?= $r['id_recherche'] ?>" 
                       class="btn btn-outline-primary fw-bold py-2 px-3 text-center small" 
                       style="border-radius: 6px; font-size: 0.85rem; border-color: #0066FF; color: #0066FF;">
                        <i class="bi bi-pencil-fill small"></i> Signer Convention
                    </a>
                    
                    <a href="validation.php?action=refuser&id=<?= $r['id_recherche'] ?>" 
                       class="btn btn-outline-danger fw-bold py-2 px-3 text-center bg-white small" 
                       style="border-radius: 6px; font-size: 0.85rem; border-color: #E07A7A; color: #E07A7A;">
                        Refuser
                    </a>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>