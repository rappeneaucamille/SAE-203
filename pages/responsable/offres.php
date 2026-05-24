<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : On autorise le Responsable OU l'Admin
if ($_SESSION['role'] !== 'Responsable stage' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}
?>

<div class="container py-4">
    <h2 class="mb-4 fw-bold">Gestion du Catalogue d'Offres</h2>
    
    <div class="card p-4 shadow-sm border-0" style="border-radius: 15px;">
        <h5 class="fw-bold text-primary mb-3"><i class="bi bi-plus-circle"></i> Publier une nouvelle offre détaillée</h5>
        
        <form action="traitement_offre.php" method="POST" class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold">Intitulé du stage</label>
                <input type="text" name="titre" class="form-control" placeholder="Ex: Designer Web" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Entreprise / Contact</label>
                <input type="text" name="ent" class="form-control" placeholder="Nom de l'entreprise ou mail contact" required>
            </div>

            <div class="col-12">
                <label class="form-label small fw-bold">Description des missions</label>
                <textarea name="desc" class="form-control" rows="3" placeholder="Détaillez les missions ici..." required></textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold">Compétences requises</label>
                <input type="text" name="competences" class="form-control" placeholder="Ex: HTML, CSS, Figma, Suite Adobe">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Lieu du stage</label>
                <input type="text" name="lieu" class="form-control" placeholder="Ex: Paris (75) ou Télétravail">
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold">Dates (Début et Fin)</label>
                <input type="text" name="dates" class="form-control" placeholder="Ex: Du 12 Mai au 14 Juillet">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Rémunération (le cas échéant)</label>
                <input type="text" name="remu" class="form-control" placeholder="Ex: 600€/mois ou Gratification légale">
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold">Promotion concernée</label>
                <select name="promo" class="form-select">
                    <option value="MMI1">MMI 1</option>
                    <option value="MMI2">MMI 2</option>
                    <option value="MMI3">MMI 3</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Année universitaire</label>
                <input type="text" name="annee" class="form-control" value="2025-2026">
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">PUBLIER L'OFFRE AU CATALOGUE</button>
            </div>
        </form>
    </div>
</div>

<div class="card p-4 shadow-sm border-0">
        <h5 class="fw-bold mb-3">Offres actuellement en ligne</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Offre</th>
                        <th>Entreprise / Lieu</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $offres = $pdo->query("SELECT * FROM Offre ORDER BY id_offre DESC")->fetchAll();
                    foreach($offres as $o): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($o['intitule']) ?></strong></td>
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($o['contact']) ?></span><br><small><?= htmlspecialchars($o['lieu']) ?></small></td>
                        <td class="small"><?= substr(htmlspecialchars($o['description']), 0, 80) ?>...</td>
                        <td>
                            <a href="supprimer_offre.php?id=<?= $o['id_offre'] ?>" 
                            class="btn btn-danger btn-sm fw-bold" 
                            onclick="return confirm('Supprimer cette offre définitivement ?')">
                                <i class="bi bi-trash"></i> SUPPRIMER
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include '../../includes/footer.php'; ?>