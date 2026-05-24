<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

if ($_SESSION['role'] !== 'Responsable stage') header('Location: ../../index.php');

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
                // 2. On crée le STAGE officiel (ce qui met à jour les stats et le suivi)
                // On insère l'entreprise dans 'lieu' et le sujet dans 'probleme' par défaut ou autre selon ton SQL
                $insertStage = $pdo->prepare("INSERT INTO Stage (num_etudiant, lieu, convention_signee) VALUES (?, ?, 'non')");
                $insertStage->execute([
                    $data['num_etudiant'], 
                    $data['entreprise_contactee']
                ]);

                // 3. On marque la recherche comme 'Validée'
                $updateRecherche = $pdo->prepare("UPDATE Recherche SET statut = 'Validée' WHERE id_recherche = ?");
                $updateRecherche->execute([$id_recherche]);

                $pdo->commit();
                echo "<div class='alert alert-success m-3 shadow-sm'>✅ Stage validé ! L'étudiant est maintenant considéré comme 'Placé' dans les statistiques.</div>";
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<div class='alert alert-danger m-3'>Erreur lors de la validation : " . $e->getMessage() . "</div>";
        }
    } 
    elseif ($_GET['action'] == 'refuser') {
        $pdo->prepare("UPDATE Recherche SET statut = 'Refusé' WHERE id_recherche = ?")->execute([$id_recherche]);
        echo "<div class='alert alert-warning m-3'>Dossier refusé. L'étudiant devra soumettre une nouvelle recherche.</div>";
    }
}

// Affichage des dossiers en attente
$recherches = $pdo->query("SELECT r.*, e.nom, e.prenom FROM Recherche r JOIN Effectuer ef ON r.id_recherche = ef.id_recherche JOIN Etudiant e ON ef.num_etudiant = e.num_etudiant WHERE r.statut = 'En attente'")->fetchAll();
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">Dossiers à valider</h2>
    
    <?php if(empty($recherches)): ?>
        <div class="alert alert-light border text-center py-5 shadow-sm">
            <i class="bi bi-check2-all display-4 text-success"></i>
            <p class="mt-3 mb-0">Tous les dossiers ont été traités. Les statistiques sont à jour.</p>
        </div>
    <?php else: ?>
        <?php foreach($recherches as $r): ?>
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1"><?= strtoupper($r['nom']) ?> <?= $r['prenom'] ?></h5>
                    <p class="mb-0 text-muted">
                        <strong>Entreprise :</strong> <?= htmlspecialchars($r['entreprise_contactee']) ?><br>
                        <strong>Sujet :</strong> <?= htmlspecialchars($r['offre_consultee']) ?>
                    </p>
                </div>
                <div>
                    <a href="validation.php?action=valider&id=<?= $r['id_recherche'] ?>" class="btn btn-success fw-bold px-4">VALIDER</a>
                    <a href="validation.php?action=refuser&id=<?= $r['id_recherche'] ?>" class="btn btn-outline-danger btn-sm ms-2">Refuser</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>