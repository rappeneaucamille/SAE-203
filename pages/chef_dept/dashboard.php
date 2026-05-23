<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité : Accès réservé au Chef de Département
if ($_SESSION['role'] !== 'Chef de département') {
    header('Location: ../../index.php');
    exit();
}

// 1. RÉCUPÉRATION DES STATISTIQUES GLOBALES
// Nombre total d'étudiants
$total_etud = $pdo->query("SELECT COUNT(*) FROM Etudiant")->fetchColumn();

// Nombre de stages validés (Statut 'Validée' dans Recherche)
$stages_valides = $pdo->query("SELECT COUNT(*) FROM Recherche WHERE statut = 'Validée'")->fetchColumn();

// Nombre de demandes en cours (Statut 'En attente')
$en_cours = $pdo->query("SELECT COUNT(*) FROM Recherche WHERE statut = 'En attente'")->fetchColumn();

// Étudiants sans stage (Ceux qui n'ont aucune recherche validée)
$sans_stage = $total_etud - $stages_valides;

// Nombre d'entreprises partenaires
$nb_entreprises = $pdo->query("SELECT COUNT(*) FROM Entreprise")->fetchColumn();

// 2. FILTRE PAR PROMOTION (Si sélectionné)
$promo_filter = isset($_GET['promo']) ? $_GET['promo'] : '';
$where_clause = $promo_filter ? "WHERE e.promotion = '$promo_filter'" : "";

// 3. RÉCUPÉRATION DES DÉTAILS PAR ÉTUDIANT
$sql = "SELECT e.nom, e.prenom, e.promotion, r.statut, r.entreprise_contactee 
        FROM Etudiant e
        LEFT JOIN Effectuer ef ON e.num_etudiant = ef.num_etudiant
        LEFT JOIN Recherche r ON ef.id_recherche = r.id_recherche
        $where_clause
        ORDER BY e.nom ASC";
$etudiants = $pdo->query($sql)->fetchAll();
?>

<div class="container">
    <h2 class="fw-bold mb-4">Tableau de Bord Direction MMI</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white p-3 shadow-sm">
                <h6 class="small uppercase">Stages Validés</h6>
                <h2 class="fw-bold"><?= $stages_valides ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark p-3 shadow-sm">
                <h6 class="small uppercase">En attente de validation</h6>
                <h2 class="fw-bold"><?= $en_cours ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white p-3 shadow-sm">
                <h6 class="small uppercase">Sans Stage Actuel</h6>
                <h2 class="fw-bold"><?= $sans_stage ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white p-3 shadow-sm">
                <h6 class="small uppercase">Entreprises & Maîtres</h6>
                <h2 class="fw-bold"><?= $nb_entreprises ?></h2>
            </div>
        </div>
    </div>

    <div class="card p-3 mb-4 shadow-sm">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="promo" class="form-select" onchange="this.form.submit()">
                    <option value="">Toutes les promotions</option>
                    <option value="MMI1" <?= $promo_filter == 'MMI1' ? 'selected' : '' ?>>MMI 1</option>
                    <option value="MMI2" <?= $promo_filter == 'MMI2' ? 'selected' : '' ?>>MMI 2</option>
                    <option value="MMI3" <?= $promo_filter == 'MMI3' ? 'selected' : '' ?>>MMI 3</option>
                </select>
            </div>
            <div class="col-md-8">
                <input type="text" id="tableSearch" class="form-control" placeholder="Rechercher un étudiant ou une entreprise...">
            </div>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">Avancement des recherches par étudiant</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Étudiant</th>
                        <th>Promo</th>
                        <th>Dernière Entreprise</th>
                        <th>Statut Recherche</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($etudiants as $e): ?>
                    <tr>
                        <td><strong><?= strtoupper($e['nom']) ?></strong> <?= $e['prenom'] ?></td>
                        <td><?= $e['promotion'] ?></td>
                        <td><?= $e['entreprise_contactee'] ?? '<span class="text-muted">Aucune démarche</span>' ?></td>
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

<?php include '../../includes/footer.php'; ?>