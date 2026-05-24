<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../../index.php'); exit(); }
$id_etud = $_SESSION['user_id'];

// --- 1. TRAITEMENT : RECHERCHE PERSONNELLE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_perso'])) {
    $entreprise = htmlspecialchars($_POST['ent_nom']);
    $sujet = htmlspecialchars($_POST['sujet']);
    $details = "Dates: " . htmlspecialchars($_POST['dates_stage']) . " | Missions: " . htmlspecialchars($_POST['missions']);
    
    $stmt = $pdo->prepare("INSERT INTO Recherche (entreprise_contactee, offre_consultee, statut, date_recherche, reponses) VALUES (?, ?, 'En attente', NOW(), ?)");
    $stmt->execute([$entreprise, $sujet, $details]);
    $id_r = $pdo->lastInsertId();
    
    $pdo->prepare("INSERT INTO Effectuer (num_etudiant, id_recherche) VALUES (?, ?)")->execute([$id_etud, $id_r]);
    echo "<div class='alert alert-success shadow-sm'>🚀 Recherche personnelle transmise !</div>";
}

// --- 2. TRAITEMENT : MISE À JOUR COMPÉTENCES ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_skills'])) {
    $comp = htmlspecialchars($_POST['competences_etud']);
    $pref = htmlspecialchars($_POST['preferences_etud']);
    $pdo->prepare("UPDATE Etudiant SET competences = ?, preferences = ? WHERE num_etudiant = ?")->execute([$comp, $pref, $id_etud]);
    header("Location: recherche.php"); exit();
}

// --- 3. RÉCUPÉRATION DES DONNÉES ---
$offres = $pdo->query("SELECT * FROM Offre ORDER BY id_offre DESC")->fetchAll();
$mesDemarches = $pdo->prepare("SELECT r.* FROM Recherche r JOIN Effectuer ef ON r.id_recherche = ef.id_recherche WHERE ef.num_etudiant = ? ORDER BY r.date_recherche DESC");
$mesDemarches->execute([$id_etud]);
$demarches = $mesDemarches->fetchAll();

$u = $pdo->prepare("SELECT competences, preferences FROM Etudiant WHERE num_etudiant = ?");
$u->execute([$id_etud]);
$u_info = $u->fetch();
?>

<div class="container-fluid px-4 py-4">
    <div class="row g-4">
        
        <div class="col-lg-4">
            
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; background-color: #f8f9fa;">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-check"></i> Mon Profil d'Affectation</h6>
                    <div class="p-2 mb-2 bg-white rounded border-start border-4 border-info">
                        <small class="text-muted d-block fw-bold">Compétences :</small>
                        <span class="small"><?= !empty($u_info['competences']) ? nl2br(htmlspecialchars($u_info['competences'])) : "<em>Non renseignées</em>" ?></span>
                    </div>
                    <div class="p-2 mb-3 bg-white rounded border-start border-4 border-info">
                        <small class="text-muted d-block fw-bold">Préférences :</small>
                        <span class="small"><?= !empty($u_info['preferences']) ? nl2br(htmlspecialchars($u_info['preferences'])) : "<em>Non renseignées</em>" ?></span>
                    </div>
                    <button class="btn btn-sm btn-outline-primary w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#modalSkills">Modifier mes informations</button>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4" style="background-color: var(--pastel-purple); border-radius: 15px;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-send-plus"></i> J'ai trouvé mon stage (Hors catalogue)</h6>
                    <form method="POST">
                        <input type="text" name="ent_nom" class="form-control form-control-sm mb-2" placeholder="Nom de l'entreprise" required>
                        <input type="text" name="sujet" class="form-control form-control-sm mb-2" placeholder="Sujet du stage" required>
                        <input type="text" name="dates_stage" class="form-control form-control-sm mb-2" placeholder="Dates précises" required>
                        <textarea name="missions" class="form-control form-control-sm mb-3" rows="2" placeholder="Missions et compétences..."></textarea>
                        <button type="submit" name="submit_perso" class="btn btn-dark btn-sm w-100 fw-bold">DÉCLARER AU RESPONSABLE</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0" style="background-color: var(--pastel-blue); border-radius: 15px;">
                <div class="card-header bg-transparent fw-bold border-0 pt-3">📋 Mes contacts récents</div>
                <div class="card-body">
                    <?php foreach($demarches as $d): ?>
                        <div class="bg-white p-2 rounded mb-2 shadow-sm border-start border-4 border-primary">
                            <div class="small fw-bold text-uppercase"><?= htmlspecialchars($d['entreprise_contactee']) ?></div>
                            <div class="small text-dark fw-bold"><i class="bi bi-file-text"></i> <?= htmlspecialchars($d['offre_consultee']) ?></div>
                            <span class="badge <?= $d['statut'] == 'Validée' ? 'bg-success' : 'bg-warning text-dark' ?>" style="font-size:0.6rem;"><?= $d['statut'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-search"></i> Catalogue des Offres MMI</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-uppercase">
                                    <th>Poste / Entreprise</th>
                                    <th>Missions & Compétences</th>
                                    <th>Détails</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($offres as $o): ?>
                                <tr>
                                    <td style="min-width: 200px;">
                                        <div class="fw-bold text-dark" style="font-size: 1.1rem;">
                                            <?= !empty($o['intitule']) ? htmlspecialchars($o['intitule']) : '<span class="text-muted italic">Intitulé non renseigné</span>' ?>
                                        </div>
                                        <div class="text-primary fw-bold small mt-1">
                                            <i class="bi bi-building"></i> 
                                            <?= !empty($o['contact']) ? htmlspecialchars($o['contact']) : '<span class="text-muted">Entreprise non renseignée</span>' ?>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="bi bi-geo-alt"></i> 
                                            <?= !empty($o['lieu']) ? htmlspecialchars($o['lieu']) : '<span class="text-muted">Lieu non renseigné</span>' ?>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="small mb-2">
                                            <strong class="text-dark">Missions :</strong><br>
                                            <?= !empty($o['description']) ? nl2br(htmlspecialchars($o['description'])) : '<span class="text-muted">Aucune description fournie</span>' ?>
                                        </div>
                                        <div class="small">
                                            <strong class="text-dark">Compétences requises :</strong><br>
                                            <?= !empty($o['competences']) ? htmlspecialchars($o['competences']) : '<span class="text-muted">Non renseignées</span>' ?>
                                        </div>
                                    </td>

                                    <td style="min-width: 180px;" class="small">
                                        <div class="mb-2">
                                            <i class="bi bi-calendar3"></i> <strong>Dates :</strong><br>
                                            <?= !empty($o['dates']) ? htmlspecialchars($o['dates']) : '<span class="text-muted">Non précisées</span>' ?>
                                        </div>
                                        <div>
                                            <i class="bi bi-currency-euro"></i> <strong>Rémunération :</strong><br>
                                            <span class="<?= !empty($o['remuneration']) ? 'text-success fw-bold' : 'text-muted' ?>">
                                                <?= !empty($o['remuneration']) ? htmlspecialchars($o['remuneration']) : "Non renseignée" ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <a href="postuler_traitement.php?id_offre=<?= $o['id_offre'] ?>" 
                                        class="btn btn-sm shadow-sm fw-bold px-3" 
                                        style="background-color: #A7C7E7 !important; color: #000 !important; border: 1px solid #0055A4; min-height: 38px; display: flex; align-items: center; justify-content: center;">
                                            POSTULER
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSkills" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Mettre à jour mon profil d'affectation</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mes Compétences</label>
                        <textarea name="competences_etud" class="form-control" rows="4"><?= htmlspecialchars($u_info['competences'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mes Préférences</label>
                        <textarea name="preferences_etud" class="form-control" rows="4"><?= htmlspecialchars($u_info['preferences'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" name="update_skills" class="btn btn-primary">Enregistrer</button></div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>