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

            // 1. On récupère les infos de la recherche et l'étudiant lié
            $query = "SELECT r.*, ef.num_etudiant 
                      FROM Recherche r 
                      JOIN Effectuer ef ON r.id_recherche = ef.id_recherche 
                      WHERE r.id_recherche = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$id_recherche]);
            $data = $stmt->fetch();

            if ($data) {
                // 2. On crée le STAGE officiel
                $insertStage = $pdo->prepare("INSERT INTO Stage (num_etudiant, lieu, convention_signee) VALUES (?, ?, 'non')");
                $insertStage->execute([
                    $data['num_etudiant'], 
                    $data['entreprise_contactee']
                ]);

                // 3. On marque la recherche comme 'Validée'
                $updateRecherche = $pdo->prepare("UPDATE Recherche SET statut = 'Validée' WHERE id_recherche = ?");
                $updateRecherche->execute([$id_recherche]);

                $pdo->commit();
                echo "<div class='alert alert-success m-3 shadow-sm'>✅ Stage validé avec succès !</div>";
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<div class='alert alert-danger m-3'>Erreur lors de la validation : " . $e->getMessage() . "</div>";
        }
    } 
    elseif ($_GET['action'] == 'refuser') {
        $pdo->prepare("UPDATE Recherche SET statut = 'Refusé' WHERE id_recherche = ?")->execute([$id_recherche]);
        echo "<div class='alert alert-warning m-3'>Dossier refusé.</div>";
    }
}

// Affichage des dossiers en attente
$recherches = $pdo->query("SELECT r.*, e.nom, e.prenom FROM Recherche r JOIN Effectuer ef ON r.id_recherche = ef.id_recherche JOIN Etudiant e ON ef.num_etudiant = e.num_etudiant WHERE r.statut = 'En attente' ORDER BY r.date_recherche ASC")->fetchAll();
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-clipboard-check"></i> Dossiers de stage à valider</h2>
    
    <?php if(empty($recherches)): ?>
        <div class="alert alert-light border text-center py-5 shadow-sm">
            <i class="bi bi-check2-all display-4 text-success"></i>
            <p class="mt-3 mb-0 text-muted">Tous les dossiers ont été traités.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach($recherches as $r): 
                // --- EXTRACTION DU MAÎTRE DE STAGE ---
                $lignes = explode("\n", $r['reponses']);
                $tuteur_nom = "";
                $tuteur_prenom = "";
                
                foreach($lignes as $l) {
                    if(stripos($l, 'NOM :') !== false) {
                        $tuteur_nom = trim(str_ireplace('NOM :', '', $l));
                    }
                    if(stripos($l, 'PRÉNOM :') !== false) {
                        $tuteur_prenom = trim(str_ireplace('PRÉNOM :', '', $l));
                    }
                }
                $maitre_complet = trim($tuteur_prenom . " " . $tuteur_nom);
                if(empty($maitre_complet)) $maitre_complet = "Non renseigné";
                // -------------------------------------
            ?>
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0" style="border-left: 5px solid #0d6efd;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3 border-end">
                                <h5 class="fw-bold text-primary mb-1"><?= strtoupper($r['nom']) ?> <?= $r['prenom'] ?></h5>
                                <div class="badge bg-light text-dark border mb-2">Soumis le <?= date('d/m/Y', strtotime($r['date_recherche'])) ?></div>
                                <p class="mb-0 small">
                                    <strong><i class="bi bi-building"></i> Entreprise :</strong><br>
                                    <?= htmlspecialchars($r['entreprise_contactee']) ?>
                                </p>
                            </div>

                            <div class="col-md-3 border-end">
                                <p class="mb-0">
                                    <strong><i class="bi bi-person-badge"></i> Maître de Stage :</strong><br>
                                    <span class="text-primary fw-bold"><?= htmlspecialchars($maitre_complet) ?></span>
                                </p>
                            </div>

                            <div class="col-md-4">
                                <p class="mb-1 small"><strong><i class="bi bi-file-earmark-text"></i> Sujet :</strong> <?= htmlspecialchars($r['offre_consultee']) ?></p>
                                <div class="p-2 bg-light rounded" style="font-size: 0.8rem; max-height: 100px; overflow-y: auto;">
                                    <strong>Détails :</strong><br>
                                    <span class="text-muted italic"><?= !empty($r['reponses']) ? nl2br(htmlspecialchars($r['reponses'])) : "<em>Aucun détail supplémentaire</em>" ?></span>
                                </div>
                            </div>

                            <div class="col-md-2 text-center">
                                <div class="d-grid gap-2">
                                    <a href="validation.php?action=valider&id=<?= $r['id_recherche'] ?>" class="btn btn-success fw-bold btn-sm">
                                        <i class="bi bi-check-lg"></i> VALIDER
                                    </a>
                                    <a href="validation.php?action=refuser&id=<?= $r['id_recherche'] ?>" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x-lg"></i> Refuser
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>