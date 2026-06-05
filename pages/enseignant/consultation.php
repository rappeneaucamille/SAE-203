<?php 
require_once '../../includes/db.php';
include '../../includes/header.php';

if ($_SESSION['role'] !== 'Enseignant standard' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// 1. Récupération des données réelles de la base
$sql = "SELECT e.nom, e.prenom, e.promotion, s.id_stage 
        FROM Etudiant e 
        LEFT JOIN Stage s ON e.num_etudiant = s.num_etudiant 
        ORDER BY e.nom ASC";
$etudiants = $pdo->query($sql)->fetchAll();
?>

<div class="container py-5" style="max-width: 1140px;">
    <h1 class="fw-bold mb-4 text-start" style="color: #2E4588; font-size: 2.2rem; letter-spacing: -0.5px;">Consultation des Etudiants</h1>
    
    <div class="bg-white p-5 text-dark" style="border-radius: 24px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.07), 0 5px 15px rgba(0, 0, 0, 0.04);">
        
        <div class="p-2 mb-5 border" style="border-radius: 12px; background-color: #FFF;">
            <div class="input-group align-items-center px-2">
                <span class="text-muted bg-transparent border-0 me-2"><i class="bi bi-search" style="font-size: 1.1rem;"></i></span>
                <input type="text" id="tableSearch" class="form-control bg-transparent border-0 shadow-none ps-1" placeholder="Rechercher un nom, une promotion ou un statut..." style="font-size: 0.95rem;">
            </div>
        </div>

        <div class="row pb-3 mb-2 border-bottom text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">
            <div class="col-md-5 ps-4">Nom & Prénom</div>
            <div class="col-md-3 text-center">Promotion</div>
            <div class="col-md-4 text-end pe-4">Statut Stage</div>
        </div>

        <div id="studentContainer">
            <?php if (empty($etudiants)): ?>
                <div class="text-center text-muted py-5">Aucun étudiant trouvé dans la base de données.</div>
            <?php else: ?>
                <?php foreach($etudiants as $et): ?>
                    <div class="search-item row align-items-center py-4 border-bottom m-0">
                        
                        <div class="col-md-5 ps-4">
                            <span class="fw-bold text-dark fs-5" style="letter-spacing: -0.2px;">
                                <span class="text-uppercase"><?= htmlspecialchars($et['nom']) ?></span> <?= htmlspecialchars($et['prenom']) ?>
                            </span>
                        </div>
                        
                        <div class="col-md-3 d-flex justify-content-center">
                            <div class="text-white fw-bold text-center d-flex align-items-center justify-content-center" 
                                 style="background-color: #6C757D; width: 55px; height: 32px; border-radius: 6px; font-size: 0.85rem; letter-spacing: 0.5px;">
                                <?= htmlspecialchars($et['promotion'] ?? 'N/A') ?>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-end pe-4">
                            <?php if($et['id_stage']): ?>
                                <span class="badge px-4 py-2 rounded-pill fw-medium align-middle" 
                                      style="background-color: #D1E7DD !important; color: #0F5132 !important; border: 1px solid #BADBCC; font-size: 0.9rem;">
                                    <i class="bi bi-check-circle-fill me-1"></i> Affecté(e)
                                </span>
                            <?php else: ?>
                                <span class="badge px-4 py-2 rounded-pill fw-medium align-middle" 
                                      style="background-color: #FFF3CD !important; color: #664D03 !important; border: 1px solid #FFECB5; font-size: 0.9rem;">
                                    <i class="bi bi-clock-fill me-1"></i> En Attente
                                </span>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
// Filtre de recherche dynamique adapté à la mise en page à plat
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let items = document.querySelectorAll(".search-item");
    
    items.forEach(function(item) {
        let text = item.textContent.toUpperCase();
        if (text.includes(filter)) {
            item.style.setProperty('display', 'flex', 'important');
        } else {
            item.style.setProperty('display', 'none', 'important');
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>