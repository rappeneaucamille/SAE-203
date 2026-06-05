<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité : Accès réservé au Chef de Département ou Administrateur
if ($_SESSION['role'] !== 'Chef de département' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

$promo_filter = isset($_GET['promo']) ? $_GET['promo'] : (isset($_SESSION['promotion_chef']) ? $_SESSION['promotion_chef'] : '');
$where_clause = $promo_filter ? "WHERE e.promotion = :promo" : "";

// --- CALCULS DES STATISTIQUES GLOBALES ---
$sql_total = "SELECT COUNT(DISTINCT num_etudiant) FROM Etudiant " . ($promo_filter ? "WHERE promotion = :promo" : "");
$stmt_total = $pdo->prepare($sql_total);
if($promo_filter) $stmt_total->bindParam(':promo', $promo_filter);
$stmt_total->execute();
$total_etud = $stmt_total->fetchColumn();

$sql_valides = "SELECT COUNT(DISTINCT ef.num_etudiant) 
                FROM Effectuer ef 
                JOIN Recherche r ON ef.id_recherche = r.id_recherche 
                JOIN Etudiant e ON ef.num_etudiant = e.num_etudiant 
                WHERE r.statut = 'Validée' " . ($promo_filter ? "AND e.promotion = :promo" : "");
$stmt_valides = $pdo->prepare($sql_valides);
if($promo_filter) $stmt_valides->bindParam(':promo', $promo_filter);
$stmt_valides->execute();
$stages_valides = $stmt_valides->fetchColumn();

$sql_attente = "SELECT COUNT(DISTINCT ef.num_etudiant) 
                FROM Effectuer ef 
                JOIN Recherche r ON ef.id_recherche = r.id_recherche 
                JOIN Etudiant e ON ef.num_etudiant = e.num_etudiant 
                WHERE r.statut = 'En attente' " . ($promo_filter ? "AND e.promotion = :promo" : "");
$stmt_attente = $pdo->prepare($sql_attente);
if($promo_filter) $stmt_attente->bindParam(':promo', $promo_filter);
$stmt_attente->execute();
$en_cours = $stmt_attente->fetchColumn();

$sans_stage = max(0, $total_etud - $stages_valides);


// --- REQUÊTE UNIQUEMENT PAR ÉTUDIANT (SANS DUPLICATION) ---
$sql = "SELECT 
            e.nom, 
            e.prenom, 
            e.promotion,
            COALESCE(MAX(CASE WHEN r.statut = 'Validée' THEN 'Validée' END), 
                     MAX(CASE WHEN r.statut = 'En attente' THEN 'En attente' END), 
                     'Pas de démarche') AS statut,
            MAX(CASE WHEN r.statut = 'Validée' THEN r.entreprise_contactee 
                     ELSE (CASE WHEN r.statut = 'En attente' THEN r.entreprise_contactee END) END) AS entreprise_contactee,
            MAX(CASE WHEN r.statut = 'Validée' THEN r.reponses 
                     ELSE (CASE WHEN r.statut = 'En attente' THEN r.reponses END) END) AS reponses
        FROM Etudiant e
        LEFT JOIN Effectuer ef ON e.num_etudiant = ef.num_etudiant
        LEFT JOIN Recherche r ON ef.id_recherche = r.id_recherche
        $where_clause
        GROUP BY e.num_etudiant, e.nom, e.prenom, e.promotion
        ORDER BY e.nom ASC";

$stmt_list = $pdo->prepare($sql);
if($promo_filter) $stmt_list->bindParam(':promo', $promo_filter);
$stmt_list->execute();
$etudiants = $stmt_list->fetchAll();
?>

<link rel="stylesheet" href="../../assets/css/style.css">

<style>
    .card-stats, .card-stats h2, .card-stats span {
        color: #FFFFFF !important;
    }
</style>

<div class="container py-5" style="max-width: 1140px;">
    <h1 class="fw-bold mb-4 text-start position-relative pe-5" style="color: #2E4588; font-size: 2.2rem; letter-spacing: -0.5px; padding-right: 280px !important;">
        Tableau de Bord Direction MMI
        
        <span class="badge px-4 py-2 rounded-3 fw-bold text-white shadow-sm" 
              style="background-color: #DC3545; font-size: 0.85rem; letter-spacing: 0.5px; position: absolute; right: 0; top: 50%; transform: translateY(-50%); white-space: nowrap;">
            SESSION CHEF DE DEPARTEMENT
        </span>
    </h1>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card card-stats p-4 border-0 shadow-sm" style="background-color: #71B999; border-radius: 16px;">
                <span class="text-uppercase fw-bold opacity-75" style="font-size: 0.8rem; letter-spacing: 0.5px;">STAGES VALIDÉS</span>
                <h2 class="fw-bold mt-2 mb-0" style="font-size: 2.5rem; letter-spacing: -1px;"><?= $stages_valides ?> / <?= $total_etud ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stats p-4 border-0 shadow-sm" style="background-color: #F5CD5F; border-radius: 16px;">
                <span class="text-uppercase fw-bold opacity-75" style="font-size: 0.8rem; letter-spacing: 0.5px;">EN ATTENTE DE VALIDATION</span>
                <h2 class="fw-bold mt-2 mb-0" style="font-size: 2.5rem; letter-spacing: -1px;"><?= $en_cours ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stats p-4 border-0 shadow-sm" style="background-color: #E68289; border-radius: 16px;">
                <span class="text-uppercase fw-bold opacity-75" style="font-size: 0.8rem; letter-spacing: 0.5px;">ÉTUDIANTS SANS STAGE</span>
                <h2 class="fw-bold mt-2 mb-0" style="font-size: 2.5rem; letter-spacing: -1px;"><?= $sans_stage ?></h2>
            </div>
        </div>
    </div>

    <div class="card p-2 mb-4 border-0 bg-light" style="border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
        <form method="GET" class="row g-2 m-0 w-100 align-items-center">
            <div class="col-md-3">
                <select name="promo" class="form-select border-0 bg-white shadow-none" style="border-radius: 8px; font-size: 0.95rem; height: 42px;" onchange="this.form.submit()">
                    <option value="">Toutes les promotions</option>
                    <option value="MMI1" <?= $promo_filter == 'MMI1' ? 'selected' : '' ?>>MMI 1</option>
                    <option value="MMI2" <?= $promo_filter == 'MMI2' ? 'selected' : '' ?>>MMI 2</option>
                    <option value="MMI3" <?= $promo_filter == 'MMI3' ? 'selected' : '' ?>>MMI 3</option>
                </select>
            </div>
            <div class="col-md-9">
                <div class="input-group align-items-center bg-white px-2 border-0 w-100" style="border-radius: 8px; height: 42px;">
                    <span class="text-muted bg-transparent border-0 me-2"><i class="bi bi-search"></i></span>
                    <input type="text" id="tableSearch" class="form-control bg-transparent border-0 shadow-none ps-1" placeholder="Rechercher un nom, une entreprise ou un tuteur..." style="font-size: 0.95rem;">
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-sm border-0" style="border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.07), 0 5px 15px rgba(0, 0, 0, 0.04) !important;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="background-color: #444444 !important;">
                        <th class="py-3 ps-4 border-0 text-white fw-bold" style="font-size: 0.95rem; background: transparent;">Étudiant</th>
                        <th class="py-3 text-center border-0 text-white fw-bold" style="font-size: 0.95rem; background: transparent;">Promotion</th>
                        <th class="py-3 border-0 text-white fw-bold" style="font-size: 0.95rem; background: transparent;">Entreprise</th>
                        <th class="py-3 border-0 text-white fw-bold" style="font-size: 0.95rem; background: transparent;">Maître Stage</th>
                        <th class="py-3 pe-4 text-center border-0 text-white fw-bold" style="font-size: 0.95rem; width: 160px; background: transparent;">Statut</th>
                    </tr>
                </thead>
                <tbody id="chefTable">
                    <?php foreach($etudiants as $e): ?>
                    <tr class="search-item">
                        <td class="py-4 ps-4 fw-bold text-dark" style="font-size: 1.05rem;">
                            <span class="text-uppercase"><?= htmlspecialchars($e['nom']) ?></span> <?= htmlspecialchars($e['prenom']) ?>
                        </td>
                        
                        <td class="text-center">
                            <div class="text-white fw-bold d-inline-flex align-items-center justify-content-center" 
                                 style="background-color: #6C757D; width: 55px; height: 32px; border-radius: 6px; font-size: 0.85rem; letter-spacing: 0.5px;">
                                <?= htmlspecialchars($e['promotion'] ?? 'N/A') ?>
                            </div>
                        </td>
                        
                        <td class="text-secondary fw-semibold">
                            <?= $e['entreprise_contactee'] ? htmlspecialchars($e['entreprise_contactee']) : '<span class="text-muted fw-normal italic">Aucune</span>' ?>
                        </td>
                        
                        <td class="text-muted small py-3" style="font-size: 0.85rem; line-height: 1.5;">
                            <?php 
                            if(!empty($e['reponses']) && $e['reponses'] !== "0") {
                                $lignes = explode("\n", $e['reponses']);
                                foreach($lignes as $ligne) {
                                    if(strpos($ligne, 'NOM :') !== false || strpos($ligne, 'PRÉNOM :') !== false || strpos($ligne, 'EMAIL :') !== false) {
                                        echo htmlspecialchars($ligne) . "<br>";
                                    }
                                }
                            } else {
                                echo '<span class="text-danger fw-normal"><i class="bi bi-x-circle me-1"></i> Non renseigné</span>';
                            }
                            ?>
                        </td>

                        <td class="pe-4 text-center">
                            <?php if($e['statut'] === 'Validée'): ?>
                                <span class="badge px-3 py-2 rounded-pill fw-medium" style="background-color: #D1E7DD !important; color: #0F5132 !important; border: 1px solid #BADBCC; font-size: 0.85rem; display: inline-block; min-width: 110px;">
                                    <i class="bi bi-check-circle-fill me-1"></i> Affecté(e)
                                </span>
                            <?php elseif($e['statut'] === 'En attente'): ?>
                                <span class="badge px-3 py-2 rounded-pill fw-medium" style="background-color: #FFF3CD !important; color: #664D03 !important; border: 1px solid #FFECB5; font-size: 0.85rem; display: inline-block; min-width: 110px;">
                                    <i class="bi bi-clock-fill me-1"></i> En Attente
                                </span>
                            <?php else: ?>
                                <span class="badge px-3 py-2 rounded-pill fw-medium" style="background-color: #F8D7DA !important; color: #842029 !important; border: 1px solid #F5C2C7; font-size: 0.85rem; display: inline-block; min-width: 110px;">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> En Attente
                                </span>
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
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let rows = document.querySelectorAll("#chefTable .search-item");
    
    rows.forEach(function(row) {
        let text = row.textContent.toUpperCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>

<?php include '../../includes/footer.php'; ?>