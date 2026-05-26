<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité : Accès réservé au Chef de Département
if ($_SESSION['role'] !== 'Chef de département') {
    header('Location: ../../index.php');
    exit();
}

$promo_filter = isset($_GET['promo']) ? $_GET['promo'] : (isset($_SESSION['promotion_chef']) ? $_SESSION['promotion_chef'] : '');
$where_clause = $promo_filter ? "WHERE e.promotion = :promo" : "";

// 1. Nombre total d'étudiants 
$sql_total = "SELECT COUNT(DISTINCT num_etudiant) FROM Etudiant " . ($promo_filter ? "WHERE promotion = :promo" : "");
$stmt_total = $pdo->prepare($sql_total);
if($promo_filter) $stmt_total->bindParam(':promo', $promo_filter);
$stmt_total->execute();
$total_etud = $stmt_total->fetchColumn();

// 2. Stages Validés (On compte les ÉTUDIANTS qui ont au moins un stage validé)
$sql_valides = "SELECT COUNT(DISTINCT ef.num_etudiant) 
                FROM Effectuer ef 
                JOIN Recherche r ON ef.id_recherche = r.id_recherche 
                JOIN Etudiant e ON ef.num_etudiant = e.num_etudiant 
                WHERE r.statut = 'Validée' " . ($promo_filter ? "AND e.promotion = :promo" : "");
$stmt_valides = $pdo->prepare($sql_valides);
if($promo_filter) $stmt_valides->bindParam(':promo', $promo_filter);
$stmt_valides->execute();
$stages_valides = $stmt_valides->fetchColumn();

// 3. En attente (On compte les ÉTUDIANTS qui attendent une réponse)
$sql_attente = "SELECT COUNT(DISTINCT ef.num_etudiant) 
                FROM Effectuer ef 
                JOIN Recherche r ON ef.id_recherche = r.id_recherche 
                JOIN Etudiant e ON ef.num_etudiant = e.num_etudiant 
                WHERE r.statut = 'En attente' " . ($promo_filter ? "AND e.promotion = :promo" : "");
$stmt_attente = $pdo->prepare($sql_attente);
if($promo_filter) $stmt_attente->bindParam(':promo', $promo_filter);
$stmt_attente->execute();
$en_cours = $stmt_attente->fetchColumn();

// 4. Calcul final
$sans_stage = max(0, $total_etud - $stages_valides);

// 3. RÉCUPÉRATION DES DÉTAILS (Ajout de r.reponses pour le Maître de Stage)
$sql = "SELECT e.nom, e.prenom, e.promotion, r.statut, r.entreprise_contactee, r.reponses 
        FROM Etudiant e
        LEFT JOIN Effectuer ef ON e.num_etudiant = ef.num_etudiant
        LEFT JOIN Recherche r ON ef.id_recherche = r.id_recherche
        $where_clause
        ORDER BY e.nom ASC";
$stmt_list = $pdo->prepare($sql);
if($promo_filter) $stmt_list->bindParam(':promo', $promo_filter);
$stmt_list->execute();
$etudiants = $stmt_list->fetchAll();
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color: #2e4588;">Tableau de Bord Direction MMI</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white p-3 shadow-sm border-0">
                <h6 class="text-uppercase small">Stages Validés (<?= $promo_filter ?: 'Tous' ?>)</h6>
                <h2 class="fw-bold mb-0"><?= $stages_valides ?> / <?= $total_etud ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark p-3 shadow-sm border-0">
                <h6 class="text-uppercase small">En attente de validation</h6>
                <h2 class="fw-bold mb-0"><?= $en_cours ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white p-3 shadow-sm border-0">
                <h6 class="text-uppercase small">Étudiants sans stage</h6>
                <h2 class="fw-bold mb-0"><?= $sans_stage ?></h2>
            </div>
        </div>
    </div>

    <div class="card p-3 mb-4 shadow-sm border-0 bg-light">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="promo" class="form-select border-0 shadow-sm" onchange="this.form.submit()">
                    <option value="">Toutes les promotions</option>
                    <option value="MMI1" <?= $promo_filter == 'MMI1' ? 'selected' : '' ?>>MMI 1</option>
                    <option value="MMI2" <?= $promo_filter == 'MMI2' ? 'selected' : '' ?>>MMI 2</option>
                    <option value="MMI3" <?= $promo_filter == 'MMI3' ? 'selected' : '' ?>>MMI 3</option>
                </select>
            </div>
            <div class="col-md-8">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                    <input type="text" id="tableSearch" class="form-control border-0" placeholder="Rechercher un nom, une entreprise ou un tuteur...">
                </div>
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Étudiant</th>
                        <th>Promo</th>
                        <th>Entreprise</th>
                        <th>Maître de Stage</th> <th>Statut</th>
                    </tr>
                </thead>
                <tbody id="chefTable">
                    <?php foreach($etudiants as $e): ?>
                    <tr>
                        <td><strong><?= strtoupper($e['nom']) ?></strong> <?= $e['prenom'] ?></td>
                        <td><span class="badge bg-light text-dark border"><?= $e['promotion'] ?></span></td>
                        <td><?= $e['entreprise_contactee'] ?? '<span class="text-muted italic">Aucune</span>' ?></td>
                        
                        <td class="small">
                            <?php 
                            if(!empty($e['reponses']) && $e['reponses'] !== "0") {
                                // On cherche la ligne contenant "NOM :" dans le bloc texte
                                $lignes = explode("\n", $e['reponses']);
                                foreach($lignes as $ligne) {
                                    if(strpos($ligne, 'NOM :') !== false || strpos($ligne, 'PRÉNOM :') !== false || strpos($ligne, 'EMAIL :') !== false) {
                                        echo htmlspecialchars($ligne) . "<br>";
                                    }
                                }
                            } else {
                                echo '<span class="text-danger italic small"><i class="bi bi-x-circle"></i> Non renseigné</span>';
                            }
                            ?>
                        </td>

                        <td>
                            <?php 
                            $badge = 'bg-secondary';
                            if($e['statut'] == 'Validée') $badge = 'bg-success';
                            if($e['statut'] == 'En attente') $badge = 'bg-warning text-dark';
                            if($e['statut'] == 'Refusé') $badge = 'bg-danger';
                            ?>
                            <span class="badge <?= $badge ?>"><?= $e['statut'] ?? 'Pas de dossier' ?></span>
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
    let rows = document.querySelector("#chefTable").rows;
    for (let i = 0; i < rows.length; i++) {
        let text = rows[i].textContent.toUpperCase();
        rows[i].style.display = text.includes(filter) ? "" : "none";
    }
});
</script>

<?php include '../../includes/footer.php'; ?>