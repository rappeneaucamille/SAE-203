<?php 
require_once '../../includes/db.php';
include '../../includes/header.php';

if ($_SESSION['role'] !== 'Enseignant standard') {
    header('Location: ../../index.php');
    exit();
}

// 1. On récupère les vraies données de la base
// On lie l'étudiant à son stage (s'il en a un) pour afficher le statut réel
$sql = "SELECT e.nom, e.prenom, e.promotion, s.id_stage 
        FROM Etudiant e 
        LEFT JOIN Stage s ON e.num_etudiant = s.num_etudiant 
        ORDER BY e.nom ASC";
$etudiants = $pdo->query($sql)->fetchAll();
?>

<div class="container py-4">
    <h2 class="mb-4 fw-bold" style="color: #0055A4;">Consultation des Étudiants</h2>
    
    <div class="card p-4 shadow-sm border-0">
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" id="tableSearch" class="form-control" placeholder="Rechercher un nom, une promo ou un statut...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nom & Prénom</th>
                        <th>Promotion</th>
                        <th>Statut Stage</th>
                    </tr>
                </thead>
                <tbody id="studentTable">
                    <?php foreach($etudiants as $et): ?>
                    <tr>
                        <td><strong><?= strtoupper($et['nom']) ?></strong> <?= $et['prenom'] ?></td>
                        <td><span class="badge bg-secondary"><?= $et['promotion'] ?></span></td>
                        <td>
                            <?php if($et['id_stage']): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Affecté(e)</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> En recherche</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Logique de recherche en temps réel
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let rows = document.querySelector("#studentTable").rows;
    
    for (let i = 0; i < rows.length; i++) {
        // On récupère tout le texte de la ligne pour une recherche globale
        let rowText = rows[i].textContent.toUpperCase();
        
        if (rowText.indexOf(filter) > -1) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }      
    }
});
</script>

<?php include '../../includes/footer.php'; ?>