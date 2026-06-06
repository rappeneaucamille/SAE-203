<?php 
require_once '../../includes/db.php';
include '../../includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// --- LOGIQUE DE VALIDATION DU COMPTE ---
if (isset($_GET['validate_user']) && isset($_GET['type'])) {
    $id = trim($_GET['validate_user']);
    $type = trim($_GET['type']);
    
    if ($type === 'prof') {
        $stmt = $pdo->prepare("UPDATE Enseignant SET statut_compte = 'Validé' WHERE LOWER(identifiant) = LOWER(?)");
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->prepare("UPDATE Etudiant SET statut_compte = 'Validé' WHERE num_etudiant = ?");
        $stmt->execute([$id]);
    }
    header('Location: gestion.php?status=validated');
    exit();
}

// --- LOGIQUE DE SUPPRESSION ---
if (isset($_GET['delete_user']) && isset($_GET['type'])) {
    $id = trim($_GET['delete_user']);
    $type = trim($_GET['type']);
    
    if ($type === 'prof') {
        $pdo->prepare("DELETE FROM Enseignant WHERE LOWER(identifiant) = LOWER(?)")->execute([$id]);
    } else {
        $pdo->prepare("DELETE FROM stage WHERE num_etudiant = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM Etudiant WHERE num_etudiant = ?")->execute([$id]);
    }
    header('Location: gestion.php?status=deleted');
    exit();
}

$profs = $pdo->query("SELECT * FROM Enseignant ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
$etudiants = $pdo->query("SELECT * FROM Etudiant ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../../assets/css/style.css">
<div class="container py-5" style="max-width: 1140px;">
    
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <h1 class="fw-bold m-0" style="color: #1E3A8A; font-size: 2.2rem; letter-spacing: -0.5px;">
            Administration
        </h1>
        <span class="badge px-4 py-2 rounded-3 fw-bold text-white shadow-sm" style="background-color: #DC3545; font-size: 0.85rem; letter-spacing: 0.5px;">SESSION ADMINISTRATEUR</span>

        
        <div class="p-1 d-inline-flex gap-1" style="background-color: #F1F5F9; border-radius: 8px;">
            <a href="../responsable/dashboard.php" class="btn fw-medium d-inline-flex align-items-center gap-2 px-3 py-1.5" style="border-radius: 6px; font-size: 0.85rem; background-color: #0056B3; color: #FFFFFF; border: none;">
                <i class="bi bi-briefcase"></i> Mode Responsable
            </a>
            <a href="../jury/notes.php" class="btn fw-medium d-inline-flex align-items-center gap-2 px-3 py-1.5" style="border-radius: 6px; font-size: 0.85rem; background-color: #64748B; color: #FFFFFF; border: none;">
                <i class="bi bi-mortarboard"></i> Mode Jury
            </a>
        </div>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'validated'): ?>
        <div class="alert alert-success border-0 px-4 py-3 mb-4 shadow-sm" style="border-radius: 10px;">✅ Le compte a été validé avec succès !</div>
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
        <div class="alert alert-danger border-0 px-4 py-3 mb-4 shadow-sm" style="border-radius: 10px;">❌ Le compte a été supprimé définitivement.</div>
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'added'): ?>
        <div class="alert alert-success border-0 px-4 py-3 mb-4 shadow-sm" style="border-radius: 10px;">✅ Le compte a été ajouté avec succès.</div>
    <?php endif; ?>

    <div class="position-relative mb-5">
        <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary opacity-60">
            <i class="bi bi-search"></i>
        </span>
        <input type="text" id="tableSearch" class="form-control py-2.5 ps-5 border border-light-subtle shadow-sm" 
               placeholder="Rechercher un nom, un email, une promo..." 
               style="border-radius: 10px; font-size: 0.95rem; background-color: #FFFFFF;">
    </div>

    <div class="border-0 shadow-sm overflow-hidden mb-5" style="border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.04) !important; background-color: #FFFFFF;">
        <div class="px-4 py-3 d-flex justify-content-between align-items-center" style="background-color: #334155;">
            <h4 class="m-0 fw-bold text-white" style="font-size: 1.25rem; letter-spacing: -0.3px;">Enseignant & Staff</h4>
            <a href="add_enseignant.php" class="btn btn-success fw-medium btn-sm d-inline-flex align-items-center gap-1 px-3 py-1.5" style="border-radius: 6px; background-color: #10B981; border: none; font-size: 0.85rem;">
                <i class="bi bi-plus-lg"></i> Ajouter Enseignant
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle mb-0 bg-white">
                <thead style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #F1F5F9;">
                    <tr>
                        <th class="ps-4 py-3 text-secondary fw-semibold w-50">Nom</th>
                        <th class="py-3 text-secondary fw-semibold">Rôle</th>
                        <th class="pe-4 py-3 text-secondary fw-semibold text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="adminTableProf" style="font-size: 0.95rem; color: #334155;">
                    <?php foreach($profs as $p): 
                        $raw_statut = $p['statut_compte'] ?? $p['Statut_compte'] ?? '';
                        $email_prof = $p['identifiant'] ?? $p['Identifiant'];
                        $is_prof_en_attente = (stripos($raw_statut, 'attente') !== false);
                    ?>
                    <tr class="align-middle" style="border-bottom: 1px solid #F1F5F9;">
                        <td class="ps-4 py-3.5">
                            <span class="text-dark fw-bold"><?= strtoupper(htmlspecialchars($p['nom'])) ?></span> 
                            <span class="text-secondary ms-1"><?= htmlspecialchars($p['prenom']) ?></span>
                            <?php if($is_prof_en_attente): ?>
                                <span class="badge bg-warning text-dark ms-2 fw-medium" style="font-size: 0.75rem; border-radius: 4px;">En attente</span>
                            <?php endif; ?>
                            <div class="text-muted small mt-0.5" style="font-size: 0.8rem; opacity: 0.7;"><?= htmlspecialchars($email_prof) ?></div>
                        </td>
                        <td>
                            <span class="text-dark fw-medium" style="font-size: 0.9rem;"><?= htmlspecialchars($p['fonctions']) ?></span>
                        </td>
                        <td class="pe-4 py-3.5 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <?php if($is_prof_en_attente): ?>
                                    <a href="gestion.php?validate_user=<?= urlencode($email_prof) ?>&type=prof" class="btn btn-sm fw-bold px-3 text-white" style="background-color: #10B981; border-radius: 6px; font-size: 0.85rem;">Valider</a>
                                <?php endif; ?>
                                <a href="edit_user.php?id=<?= urlencode($email_prof) ?>&type=prof" class="btn btn-sm fw-medium px-3" style="background-color: #3B82F6; color: #FFFFFF; border-radius: 6px; font-size: 0.85rem; border: none;">Modifier</a>
                                <a href="gestion.php?delete_user=<?= urlencode($email_prof) ?>&type=prof" 
                                class="btn btn-sm fw-medium px-3 btn-confirm" 
                                style="background-color: #EF4444; color: #FFFFFF; border-radius: 6px; font-size: 0.85rem; border: none;" 
                                data-confirm="Êtes-vous sûr de vouloir supprimer ce professeur ? Cette action est irréversible.">
                                    Supprimer
                                </a>                           
                             </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="border-0 shadow-sm overflow-hidden" style="border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.04) !important; background-color: #FFFFFF;">
        <div class="px-4 py-3 d-flex justify-content-between align-items-center" style="background-color: #475569;">
            <h4 class="m-0 fw-bold text-white" style="font-size: 1.25rem; letter-spacing: -0.3px;">Étudiants</h4>
            <a href="add_etudiant.php" class="btn btn-light fw-medium btn-sm d-inline-flex align-items-center gap-1 px-3 py-1.5" style="border-radius: 6px; font-size: 0.85rem; color: #334155; border: none;">
                <i class="bi bi-plus-lg"></i> Ajouter un Étudiant
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle mb-0 bg-white">
                <thead style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #F1F5F9;">
                    <tr>
                        <th class="ps-4 py-3 text-secondary fw-semibold w-50">Nom</th>
                        <th class="py-3 text-secondary fw-semibold text-center">Promotion</th>
                        <th class="pe-4 py-3 text-secondary fw-semibold text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="adminTableEtud" style="font-size: 0.95rem; color: #334155;">
                    <?php foreach($etudiants as $e): 
                        $raw_statut_etud = $e['statut_compte'] ?? $e['Statut_compte'] ?? '';
                        $is_etud_en_attente = (stripos($raw_statut_etud, 'attente') !== false);
                        $promo = htmlspecialchars($e['promotion'] ?? 'MMI1');
                    ?>
                    <tr class="align-middle" style="border-bottom: 1px solid #F1F5F9;">
                        <td class="ps-4 py-3.5">
                            <a href="../profil_etudiant.php?id=<?= urlencode($e['num_etudiant']) ?>" class="text-decoration-none etudiant-lien">
                                <span class="text-dark fw-bold text-uppercase"><?= htmlspecialchars($e['nom']) ?></span> 
                                <span class="text-secondary ms-1"><?= htmlspecialchars($e['prenom']) ?></span>
                                <i class="bi bi-box-arrow-up-right small ms-1 icone-lien"></i>
                            </a>
                            
                            <?php if($is_etud_en_attente): ?>
                                <span class="badge bg-warning text-dark ms-2 fw-medium" style="font-size: 0.75rem; border-radius: 4px;">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 text-center">
                            <span class="badge px-3 py-2 fw-semibold" style="background-color: #64748B; color: #FFFFFF; border-radius: 6px; font-size: 0.75rem;">
                                <?= $promo ?>
                            </span>
                        </td>
                        <td class="pe-4 py-3.5 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <?php if($is_etud_en_attente): ?>
                                    <a href="gestion.php?validate_user=<?= $e['num_etudiant'] ?>&type=etud" class="btn btn-sm fw-bold px-3 text-white" style="background-color: #10B981; border-radius: 6px; font-size: 0.85rem;">Valider</a>
                                <?php endif; ?>
                                <a href="edit_user.php?id=<?= $e['num_etudiant'] ?>&type=etud" class="btn btn-sm fw-medium px-3" style="background-color: #3B82F6; color: #FFFFFF; border-radius: 6px; font-size: 0.85rem; border: none;">Modifier</a>
                                <a href="gestion.php?delete_user=<?= $e['num_etudiant'] ?>&type=etud" 
                                class="btn btn-sm fw-medium px-3 btn-confirm" 
                                style="background-color: #EF4444; color: #FFFFFF; border-radius: 6px; font-size: 0.85rem; border: none;" 
                                data-confirm="Êtes-vous sûr de vouloir supprimer cet étudiant ? Cette action effacera ses données.">
                                    Supprimer
                                </a>                          
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Filtre de recherche
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let rows = document.querySelectorAll("tbody tr");
    rows.forEach(row => {
        row.style.display = row.textContent.toUpperCase().includes(filter) ? "" : "none";
    });
});
</script>

<?php include '../../includes/footer.php'; ?>