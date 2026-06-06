<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../../index.php'); exit(); }
$id_etud = $_SESSION['user_id'];

// --- 1. TRAITEMENT : RECHERCHE PERSONNELLE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_perso'])) {
    $entreprise = trim(htmlspecialchars($_POST['ent_nom']));
    $sujet = htmlspecialchars($_POST['sujet']);
    
    // Récupération des infos du maître de stage
    $mds_nom = trim(htmlspecialchars($_POST['mds_nom']));
    $mds_prenom = trim(htmlspecialchars($_POST['mds_prenom']));
    $mds_email = trim(htmlspecialchars($_POST['mds_email']));
    $mds_tel = isset($_POST['mds_tel']) ? trim(htmlspecialchars($_POST['mds_tel'])) : '';

    // Compilation pour la colonne texte "reponses" 
    $details = "DATES : " . htmlspecialchars($_POST['dates_stage']) . "\n";
    $details .= "MISSIONS : " . htmlspecialchars($_POST['missions']) . "\n";
    $details .= "--- INFOS MAÎTRE DE STAGE ---\n";
    $details .= "NOM : " . $mds_nom . "\n";
    $details .= "PRÉNOM : " . $mds_prenom . "\n";
    $details .= "EMAIL : " . $mds_email;
    
    try {
        $pdo->beginTransaction();

        // ==========================================
        // A. TRAITEMENT AUTOMATIQUE DU MAÎTRE DE STAGE
        // ==========================================
        $stmtCheckMaitre = $pdo->prepare("SELECT id_maitre FROM maitre_de_stage WHERE email = ?");
        $stmtCheckMaitre->execute([$mds_email]);
        $maitre_existant = $stmtCheckMaitre->fetch();

        if ($maitre_existant) {
            $id_maitre = $maitre_existant['id_maitre'];
        } else {
            // Le maître de stage est nouveau, on lui crée un compte temporaire
            // On génère un mot de passe temporaire crypté au cas où
            $pwd_temporaire = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
            
            $stmtInsMaitre = $pdo->prepare("INSERT INTO maitre_de_stage (identifiant, pwd, nom, prenom, email, tel) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtInsMaitre->execute([
                $mds_email,       // identifiant
                $pwd_temporaire,  // pwd crypté
                $mds_nom,         // nom
                $mds_prenom,      // prenom
                $mds_email,       // email
                $mds_tel          // tel
            ]);
            $id_maitre = $pdo->lastInsertId();
        }

        // ==========================================
        // B. TRAITEMENT AUTOMATIQUE DE L'ENTREPRISE
        // ==========================================
        $stmtCheckEnt = $pdo->prepare("SELECT id_ent FROM entreprise WHERE nom = ?");
        $stmtCheckEnt->execute([$entreprise]);
        $ent_existante = $stmtCheckEnt->fetch();

        if ($ent_existante) {
            $id_ent = $ent_existante['id_ent'];
        } else {
            $stmtInsEnt = $pdo->prepare("INSERT INTO entreprise (nom, adresse, contact) VALUES (?, ?, ?)");
            $stmtInsEnt->execute([$entreprise, "Renseignée par l'étudiant", $mds_nom . " " . $mds_prenom]);
            $id_ent = $pdo->lastInsertId();
        }

        // ==========================================
        // C. ENREGISTREMENT DE LA RECHERCHE
        // ==========================================
        $stmt = $pdo->prepare("INSERT INTO Recherche (entreprise_contactee, offre_consultee, statut, date_recherche, reponses) VALUES (?, ?, 'En attente', NOW(), ?)");
        $stmt->execute([$entreprise, $sujet, $details]);
        $id_r = $pdo->lastInsertId();
        
        // Liaison Étudiant <-> Recherche
        $pdo->prepare("INSERT INTO Effectuer (num_etudiant, id_recherche) VALUES (?, ?)")->execute([$id_etud, $id_r]);
        
        $pdo->commit();
        echo "<div class='alert alert-success shadow-sm'>🚀 Demande envoyée ! L'entreprise et le Maître de stage ont été enregistrés en base de données.</div>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<div class='alert alert-danger shadow-sm'>Erreur lors de la soumission : " . $e->getMessage() . "</div>";
    }
}
// --- 2. TRAITEMENT : MISE À JOUR COMPÉTENCES ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_skills'])) {
    $comp = htmlspecialchars($_POST['competences_etud']);
    $pref = htmlspecialchars($_POST['preferences_etud']);
    $pdo->prepare("UPDATE Etudiant SET competences = ?, preferences = ? WHERE num_etudiant = ?")->execute([$comp, $pref, $id_etud]);
    header("Location: recherche.php"); exit();
}

// --- 3. GESTION DU MOTEUR DE RECHERCHE ET DES FILTRES ---
$search_text = isset($_GET['search_text']) ? trim($_GET['search_text']) : '';
$filter_lieu = isset($_GET['filter_lieu']) ? trim($_GET['filter_lieu']) : '';
$filter_remun = isset($_GET['filter_remun']) ? trim($_GET['filter_remun']) : '';

// Construction dynamique de la requête SQL
$queryStr = "SELECT * FROM Offre WHERE 1=1";
$params = [];

if ($search_text !== '') {
    $queryStr .= " AND (intitule LIKE ? OR contact LIKE ? OR description LIKE ?)";
    $params[] = "%$search_text%";
    $params[] = "%$search_text%";
    $params[] = "%$search_text%";
}

if ($filter_lieu !== '') {
    $queryStr .= " AND lieu = ?";
    $params[] = $filter_lieu;
}

if ($filter_remun === 'gratuit') {
    $queryStr .= " AND (remuneration IS NULL OR remuneration = 0)";
} elseif ($filter_remun === 'remunere') {
    $queryStr .= " AND remuneration > 0";
}

$queryStr .= " ORDER BY id_offre DESC";

$stmtOffres = $pdo->prepare($queryStr);
$stmtOffres->execute($params);
$offres = $stmtOffres->fetchAll();

// Récupération des localisations distinctes existantes en BDD pour alimenter le filtre automatiquement
$lieux_disponibles = $pdo->query("SELECT DISTINCT lieu FROM Offre WHERE lieu IS NOT NULL AND lieu != '' ORDER BY lieu ASC")->fetchAll(PDO::FETCH_COLUMN);

// --- 4. RÉCUPÉRATION DES DONNÉES ÉTUDIANT ---
$mesDemarches = $pdo->prepare("SELECT r.* FROM Recherche r JOIN Effectuer ef ON r.id_recherche = ef.id_recherche WHERE ef.num_etudiant = ? ORDER BY r.date_recherche DESC");
$mesDemarches->execute([$id_etud]);
$demarches = $mesDemarches->fetchAll();

$u = $pdo->prepare("SELECT competences, preferences FROM Etudiant WHERE num_etudiant = ?");
$u->execute([$id_etud]);
$u_info = $u->fetch();
?>

<link rel="stylesheet" href="../../assets/css/style.css">
<div class="container py-4" style="max-width: 1100px;">
    
    <div class="row g-4 mb-5">
        <div class="col-md-5">
            <div class="card h-100 shadow border-0 p-3" style="border-radius: 20px; background-color: #ffffff;">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold text-primary mb-4 d-flex align-items-center gap-2" style="color: #0066FF !important;">
                            <i class="bi bi-person-check fs-4"></i> Mon Profil d'Affectation
                        </h5>
                        <div class="mb-3">
                            <small class="text-muted d-block fw-bold mb-1">Compétences :</small>
                            <p class="text-dark small bg-light p-3 rounded-3" style="min-height: 60px;">
                                <?= !empty($u_info['competences']) ? nl2br(htmlspecialchars($u_info['competences'])) : "<em>Non renseignées</em>" ?>
                            </p>
                        </div>
                        <div class="mb-4">
                            <small class="text-muted d-block fw-bold mb-1">Préférences :</small>
                            <p class="text-dark small bg-light p-3 rounded-3" style="min-height: 60px;">
                                <?= !empty($u_info['preferences']) ? nl2br(htmlspecialchars($u_info['preferences'])) : "<em>Non renseignées</em>" ?>
                            </p>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary btn-sm rounded-pill py-2 fw-bold w-100" style="border-color: #0066FF; color: #0066FF;" data-bs-toggle="modal" data-bs-target="#modalSkills">Modifier mes informations</button>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card h-100 shadow border-0 p-3" style="border-radius: 20px; background-color: #ffffff;">
                <div class="card-body">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-send-plus fs-5"></i> J'ai trouvé mon stage (Hors catalogue)
                    </h6>
                    <form method="POST">
                        <small class="text-muted d-block mb-1">L'entreprise :</small>
                        <input type="text" name="ent_nom" class="form-control form-control-sm mb-2 rounded-3 bg-light border-0" placeholder="Nom de l'entreprise" required>
                        <input type="text" name="sujet" class="form-control form-control-sm mb-2 rounded-3 bg-light border-0" placeholder="Sujet du stage" required>
                        <input type="text" name="dates_stage" class="form-control form-control-sm mb-3 rounded-3 bg-light border-0" placeholder="Dates précises" required>
                        
                        <small class="text-muted d-block mb-1">Le Maître de Stage :</small>
                        <div class="row g-2 mb-2">
                            <div class="col-6"><input type="text" name="mds_nom" class="form-control form-control-sm rounded-3 bg-light border-0" placeholder="Nom" required></div>
                            <div class="col-6"><input type="text" name="mds_prenom" class="form-control form-control-sm rounded-3 bg-light border-0" placeholder="Prénom" required></div>
                        </div>
                        <input type="email" name="mds_email" class="form-control form-control-sm mb-3 rounded-3 bg-light border-0" placeholder="Email du tuteur" required>

                        <small class="text-muted d-block mb-1">Missions :</small>
                        <textarea name="missions" class="form-control form-control-sm mb-4 rounded-3 bg-light border-0" rows="2" placeholder="Missions et compétences..."></textarea>
                        
                        <button type="submit" name="submit_perso" class="btn btn-dark btn-sm rounded-pill w-100 fw-bold py-2 text-uppercase" style="letter-spacing: 0.5px; background-color: #1a1a1a;">Déclarer au responsable</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow border-0 p-2 mb-4" style="border-radius: 15px; background-color: #ffffff;">
        <div class="card-body p-2">
            <form method="GET" action="recherche.php" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-search"></i> Mots-clés</label>
                    <input type="text" name="search_text" class="form-control form-control-sm bg-light border-0 rounded-3" placeholder="Ex: Développeur, Apple..." value="<?= htmlspecialchars($search_text) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-geo-alt"></i> Localisation</label>
                    <select name="filter_lieu" class="form-select form-select-sm bg-light border-0 rounded-3">
                        <option value="">Tous les lieux</option>
                        <?php foreach($lieux_disponibles as $l): ?>
                            <option value="<?= htmlspecialchars($l) ?>" <?= $filter_lieu === $l ? 'selected' : '' ?>><?= htmlspecialchars($l) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-currency-euro"></i> Rémunération</label>
                    <select name="filter_remun" class="form-select form-select-sm bg-light border-0 rounded-3">
                        <option value="">Toutes</option>
                        <option value="remunere" <?= $filter_remun === 'remunere' ? 'selected' : '' ?>>Gratification payée</option>
                        <option value="gratuit" <?= $filter_remun === 'gratuit' ? 'selected' : '' ?>>Non gratifié / Non spécifié</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 w-100 py-2 fw-bold" style="background-color: #0066FF;"><i class="bi bi-funnel-fill"></i> Filtrer</button>
                    <?php if($search_text !== '' || $filter_lieu !== '' || $filter_remun !== ''): ?>
                        <a href="recherche.php" class="btn btn-outline-secondary btn-sm rounded-3 d-flex align-items-center justify-content-center px-2" title="Réinitialiser"><i class="bi bi-arrow-clockwise"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-5">
        <h4 class="fw-bold text-primary mb-4 d-flex align-items-center gap-2" style="color: #0066FF !important;">
            <i class="bi bi-search fs-4"></i> Catalogue des Offres MMI
            <span class="fs-6 text-muted fw-normal">(<?= count($offres) ?> offre(s))</span>
        </h4>

        <?php if(empty($offres)): ?>
            <div class="card shadow-sm border-0 text-center py-5 text-muted" style="border-radius: 20px;">
                <i class="bi bi-emoji-frown fs-2 mb-2"></i>
                <p class="mb-0">Aucune offre ne correspond à vos critères de recherche.</p>
            </div>
        <?php else: ?>
            <?php foreach($offres as $o): ?>
                <div class="card shadow border-0 p-3 mb-3 transition-hover" style="border-radius: 20px; background-color: #ffffff;">
                    <div class="card-body">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-4">
                                <h4 class="fw-bold text-dark mb-2" style="font-size: 1.4rem;">
                                    <?= !empty($o['intitule']) ? htmlspecialchars($o['intitule']) : '<span class="text-muted italic">Intitulé non renseigné</span>' ?>
                                </h4>
                                <div class="text-primary fw-bold mb-1 d-flex align-items-center gap-1" style="color: #0066FF !important; font-size: 1.05rem;">
                                    <i class="bi bi-building"></i> 
                                    <?= !empty($o['contact']) ? htmlspecialchars($o['contact']) : '<span class="text-muted">Entreprise non renseignée</span>' ?>
                                </div>
                                <div class="text-muted small d-flex align-items-center gap-1">
                                    <i class="bi bi-geo-alt"></i> 
                                    <?= !empty($o['lieu']) ? htmlspecialchars($o['lieu']) : '<span class="text-muted">Lieu non renseigné</span>' ?>
                                </div>
                            </div>

                            <div class="col-lg-4 px-lg-3 border-start border-light">
                                <div class="small mb-2">
                                    <strong class="text-dark d-block mb-1">Missions :</strong>
                                    <span class="text-muted text-truncate-custom">
                                        <?= !empty($o['description']) ? nl2br(htmlspecialchars($o['description'])) : 'Aucune description.' ?>
                                    </span>
                                </div>
                                <div class="small">
                                    <strong class="text-dark d-block mb-1">Compétences :</strong>
                                    <span class="text-muted">
                                        <?= !empty($o['competences']) ? htmlspecialchars($o['competences']) : 'Non renseignées.' ?>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-2 px-lg-3 border-start border-light small">
                                <div class="mb-2">
                                    <strong class="text-dark d-block mb-1">Dates :</strong>
                                    <span class="text-muted"><?= !empty($o['dates']) ? htmlspecialchars($o['dates']) : 'Non précisées' ?></span>
                                </div>
                                <div>
                                    <strong class="text-dark d-block mb-1">Rémunération :</strong>
                                    <span class="<?= (!empty($o['remuneration']) && $o['remuneration'] > 0) ? 'text-success fw-bold' : 'text-muted' ?>" style="color: <?= (!empty($o['remuneration']) && $o['remuneration'] > 0) ? '#258754' : '' ?> !important;">
                                        <?= (!empty($o['remuneration']) && $o['remuneration'] > 0) ? htmlspecialchars($o['remuneration']) . ' €' : "0,00 €" ?>
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-2 text-center text-lg-end">
                                <a href="postuler_traitement.php?id_offre=<?= $o['id_offre'] ?>" 
                                class="btn shadow-sm fw-bold px-4 py-2 w-100 rounded-3 text-uppercase align-items-center justify-content-center" 
                                style="background-color: #2E4588; color: #FFFFFF; border: none; font-size: 0.85rem; letter-spacing: 0.5px; min-height: 40px; display: inline-flex;">
                                    Postuler
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="mb-4">
        <h4 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-file-text fs-4"></i> Mes candidatures
        </h4>

        <?php if(empty($demarches)): ?>
            <div class="card shadow-sm border-0 text-center py-4 text-muted" style="border-radius: 15px;">
                <p class="mb-0 small">Vous n'avez pas encore soumis de candidatures.</p>
            </div>
        <?php else: ?>
            <?php foreach($demarches as $d): ?>
                <div class="card shadow border-0 p-3 mb-2" style="border-radius: 15px; background-color: #ffffff;">
                    <div class="card-body py-1 d-flex justify-content-between align-items-center">
                        <div class="fw-bold text-dark" style="font-size: 1.2rem;">
                            <?= htmlspecialchars($d['offre_consultee']) ?>
                        </div>
                        
                        <?php 
                        $bg_color = '#ffc107'; // En attente (Jaune)
                        if ($d['statut'] === 'Validée') $bg_color = '#73B479'; // Vert maquette
                        if ($d['statut'] === 'Refusé') $bg_color = '#E28383'; // Rouge maquette
                        ?>
                        <span class="badge text-white px-4 py-2 rounded-pill text-uppercase fw-bold" style="font-size:0.75rem; background-color: <?= $bg_color ?>; letter-spacing: 0.5px; min-width: 140px; display: inline-block; text-center;">
                            <?= $d['statut'] === 'Refusé' ? 'REFUSÉE' : htmlspecialchars($d['statut']) ?>
                        </span>                  
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<div class="modal fade" id="modalSkills" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold m-0">Mettre à jour mon profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Mes Compétences</label>
                        <textarea name="competences_etud" class="form-control bg-light border-0 rounded-3" rows="4"><?= htmlspecialchars($u_info['competences'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Mes Préférences</label>
                        <textarea name="preferences_etud" class="form-control bg-light border-0 rounded-3" rows="4"><?= htmlspecialchars($u_info['preferences'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="submit" name="update_skills" class="btn btn-primary rounded-pill px-4 fw-bold w-100 py-2" style="background-color: #0066FF;">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>