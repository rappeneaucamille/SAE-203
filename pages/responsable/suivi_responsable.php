<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : On autorise le Responsable OU l'Admin
if ($_SESSION['role'] !== 'Responsable stage' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// LOGIQUE TECHNIQUE (CONSERVÉE ET SÉCURISÉE)
if (isset($_POST['update_suivi'])) {
    $stmt = $pdo->prepare("UPDATE stage SET probleme = ?, convention_signee = ? WHERE id_stage = ?");
    $stmt->execute([$_POST['refomulation'], $_POST['convention'], $_POST['id_stage']]);
    echo "<div class='alert alert-success m-4 border-0 shadow-sm' style='border-radius: 12px;'>✅ Modifications enregistrées avec succès.</div>";
}

// Récupération des données (e.num_etudiant est bien présent)
$sql = "SELECT s.id_stage, s.lieu, s.convention_signee, s.probleme, s.alerte_etudiant, e.num_etudiant, e.nom, e.prenom 
        FROM stage s 
        JOIN etudiant e ON s.num_etudiant = e.num_etudiant";
$res = $pdo->query($sql)->fetchAll();
?>

<link rel="stylesheet" href="../../assets/css/style.css">

<div class="container py-5" style="max-width: 1140px;">
    <h1 class="fw-bold mb-5" style="color: #1E293B; font-size: 2.2rem; letter-spacing: -0.5px;">
        Suivi & Remontée des Problèmes
    </h1>
    
    <?php if(empty($res)): ?>
        <div class="bg-white p-5 text-center border-0 shadow-sm" style="border-radius: 24px;">
            <p class="text-muted mb-0 small">Aucun stage en cours de suivi actuellement.</p>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-4">
            <?php foreach($res as $row): ?>
                <form method="POST" class="m-0">
                    <input type="hidden" name="id_stage" value="<?= $row['id_stage'] ?>">
                    
                    <div class="bg-white p-4 border-0 shadow-sm d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap gap-4" 
                         style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03), 0 2px 10px rgba(0,0,0,0.01) !important;">
                        
                        <div style="min-width: 180px; flex: 1;">
                            <h4 class="mb-1" style="font-size: 1.3rem; letter-spacing: -0.3px;">
                                <a href="../profil_etudiant.php?id=<?= urlencode($row['num_etudiant']) ?>" class="text-decoration-none text-dark fw-bold">
                                    <?= htmlspecialchars($row['prenom']) ?> <?= strtoupper($row['nom']) ?>
                                    <i class="bi bi-box-arrow-up-right small text-secondary ms-1" style="font-size: 0.85rem;"></i>
                                </a>
                            </h4>
                        </div>

                        <div style="min-width: 140px; flex: 0.8;">
                            <span class="text-secondary fw-medium" style="font-size: 1.05rem;">
                                <?= htmlspecialchars($row['lieu']) ?>
                            </span>
                        </div>

                        <div style="min-width: 280px; flex: 2;">
                            <?php if(!empty($row['alerte_etudiant'])): ?>
                                <div class="mb-2 px-2 py-1 bg-light rounded text-danger italic" style="font-size: 0.75rem; border-left: 3px solid #EF4444;">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Étudiant : "<?= htmlspecialchars($row['alerte_etudiant']) ?>"
                                </div>
                            <?php endif; ?>
                            
                            <textarea name="refomulation" 
                                      class="form-control border bg-white px-3 py-2 text-secondary" 
                                      rows="1" 
                                      style="border-radius: 8px; font-size: 0.9rem; resize: none; min-height: 43px;"
                                      placeholder="Reformuler le problème de l'étudiant ici..."><?= htmlspecialchars($row['probleme'] ?? '') ?></textarea>
                        </div>

                        <div style="min-width: 160px; flex: 1;">
                            <div class="d-flex flex-column gap-1">
                                <label class="small fw-bold text-dark mb-0" style="font-size: 0.85rem;">Convention</label>
                                <select name="convention" class="form-select text-secondary bg-white" style="border-radius: 8px; font-size: 0.9rem; height: 43px;">
                                    <option value="non" <?= $row['convention_signee'] == 'non' ? 'selected' : '' ?>>En attente</option>
                                    <option value="oui" <?= $row['convention_signee'] == 'oui' ? 'selected' : '' ?>>Signée</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-end" style="min-width: 140px;">
                            <button type="submit" name="update_suivi" 
                                    class="btn btn-primary fw-bold text-white w-100 d-flex align-items-center justify-content-center gap-2" 
                                    style="background-color: #0066FF; border: none; border-radius: 8px; height: 43px; font-size: 0.85rem; letter-spacing: 0.5px;">
                                <i class="bi bi-check2-circle" style="font-size: 1.05rem;"></i> ENREGISTRER
                            </button>
                        </div>

                    </div>
                </form>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>