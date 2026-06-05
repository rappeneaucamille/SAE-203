<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : On autorise le Responsable OU l'Admin
if ($_SESSION['role'] !== 'Responsable stage' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}
?>
<link rel="stylesheet" href="../../assets/css/style.css">

<div class="container py-5" style="max-width: 1140px;">
    <h1 class="fw-bold mb-5" style="color: #000000; font-size: 2.2rem; letter-spacing: -0.5px;">Gestion du Catalogue d'Offres</h1>
    
    <div class="bg-white p-5 mb-5 border-0" style="border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.06), 0 5px 15px rgba(0,0,0,0.03);">
        <h5 class="fw-bold text-primary mb-4 d-flex align-items-center gap-2" style="color: #0066FF !important; font-size: 1.2rem;">
            <i class="bi bi-plus-circle-fill"></i> Publier une nouvelle offre détaillée
        </h5>
        
        <form action="traitement_offre.php" method="POST" class="row g-4">
            <div class="col-md-6">
                <label class="form-label small fw-bold text-dark">Intitulé du stage</label>
                <input type="text" name="titre" class="form-control py-2.5" placeholder="Ex : Designer Web" style="border-radius: 8px; border: 1px solid #E2E8F0;" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-dark">Entreprise / Contact</label>
                <input type="text" name="ent" class="form-control py-2.5" placeholder="Nom de l'entreprise ou mail contact" style="border-radius: 8px; border: 1px solid #E2E8F0;" required>
            </div>

            <div class="col-12">
                <label class="form-label small fw-bold text-dark">Description des missions</label>
                <textarea name="desc" class="form-control py-2.5" rows="4" placeholder="Détaillez les missions ici..." style="border-radius: 8px; border: 1px solid #E2E8F0;" required></textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold text-dark">Compétences requises</label>
                <input type="text" name="competences" class="form-control py-2.5" placeholder="Ex : HTML, CSS, Figma, Suite Adobe" style="border-radius: 8px; border: 1px solid #E2E8F0;">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-dark">Lieu du stage</label>
                <input type="text" name="lieu" class="form-control py-2.5" placeholder="Ex : Paris (75) ou Télétravail" style="border-radius: 8px; border: 1px solid #E2E8F0;">
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold text-dark">Dates (Début et Fin)</label>
                <input type="text" name="dates" class="form-control py-2.5" placeholder="Ex : Du Mai au Juillet" style="border-radius: 8px; border: 1px solid #E2E8F0;">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-dark">Rémunération (le cas échéant)</label>
                <input type="text" name="remu" class="form-control py-2.5" placeholder="Ex : 600€/mois ou Gratification légale" style="border-radius: 8px; border: 1px solid #E2E8F0;">
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold text-dark">Promotion concernée</label>
                <select name="promo" class="form-select py-2.5" style="border-radius: 8px; border: 1px solid #E2E8F0;">
                    <option value="MMI1">MMI 1</option>
                    <option value="MMI2">MMI 2</option>
                    <option value="MMI3">MMI 3</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-dark">Année universitaire</label>
                <input type="text" name="annee" class="form-control py-2.5" value="2025-2026" style="border-radius: 8px; border: 1px solid #E2E8F0;">
            </div>

            <div class="col-12 mt-4 pt-2">
                <button type="submit" class="btn text-white w-100 fw-bold py-3 shadow-sm" style="background-color: #2E4588; border-radius: 8px; letter-spacing: 0.5px; font-size: 0.95rem;">PUBLIER L'OFFRE AU CATALOGUE</button>
            </div>
        </form>
    </div>

    <h3 class="fw-bold mb-4" style="color: #0066FF; font-size: 1.6rem; margin-top: 3rem;">Offres actuellement en ligne</h3>

    <div class="d-flex flex-column gap-4">
        <?php
        // On récupère les offres ordonnées par ID décroissant
        $offres = $pdo->query("SELECT * FROM Offre ORDER BY id_offre DESC")->fetchAll();
        
        if (empty($offres)): ?>
            <div class="bg-white p-5 text-center text-muted border-0 shadow-sm" style="border-radius: 20px;">
                <i class="bi bi-folder-x fs-2 mb-2 d-block"></i> Aucune offre en ligne pour le moment.
            </div>
        <?php else:
            foreach($offres as $o): ?>
            <div class="bg-white p-4 border-0 d-flex justify-content-between align-items-center flex-wrap flex-md-nowrap gap-3 position-relative" style="border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05), 0 4px 12px rgba(0,0,0,0.02);">
                
                <div style="flex: 1; min-width: 250px;">
                    <h4 class="fw-bold text-dark mb-2" style="font-size: 1.4rem; letter-spacing: -0.3px;"><?= htmlspecialchars($o['intitule'] ?? $o['titre']) ?></h4>
                    <div class="d-flex align-items-center gap-1 mb-2 text-primary fw-bold" style="color: #0066FF !important;">
                        <i class="bi bi-building"></i> <?= htmlspecialchars($o['entreprise'] ?? $o['contact']) ?>
                    </div>
                    <div class="text-muted small fw-medium">
                        <i class="bi bi-geo-alt-fill me-1"></i> <?= htmlspecialchars($o['lieu']) ?>
                    </div>
                </div>

                <div style="flex: 1.2; min-width: 280px; font-size: 0.9rem;">
                    <div class="mb-2">
                        <span class="fw-bold text-dark">Missions :</span> 
                        <span class="text-secondary"><?= htmlspecialchars($o['description'] ?? $o['missions'] ?? '') ?></span>
                    </div>
                    <div>
                        <span class="fw-bold text-dark">Compétences :</span> 
                        <span class="text-secondary"><?= htmlspecialchars($o['competences'] ?? '') ?></span>
                    </div>
                </div>

                <div style="flex: 1; min-width: 200px; font-size: 0.9rem;">
                    <div class="mb-2">
                        <span class="fw-bold text-dark">Dates :</span> 
                        <span class="text-secondary"><?= htmlspecialchars($o['dates'] ?? '') ?></span>
                    </div>
                    <div>
                        <span class="fw-bold text-dark">Rémunération :</span> 
                        <span class="text-success fw-bold"><?= htmlspecialchars($o['remu'] ?? $o['remuneration'] ?? '0.00 €') ?></span>
                    </div>
                </div>

                <div class="text-end ps-md-3">
                    <a href="supprimer_offre.php?id=<?= $o['id_offre'] ?>" 
                       class="btn text-white fw-bold px-4 py-2 d-flex align-items-center gap-2 rounded-3" 
                       style="background-color: #E07A7A; border: none; font-size: 0.85rem; letter-spacing: 0.5px;"
                       onclick="return confirm('Supprimer cette offre définitivement ?')">
                        <i class="bi bi-trash3-fill"></i> SUPPRIMER
                    </a>
                </div>

            </div>
            <?php endforeach;
        endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>