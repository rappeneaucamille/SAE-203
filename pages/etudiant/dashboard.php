<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

$id = $_SESSION['user_id'];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "UPDATE Etudiant SET nom=?, prenom=?, tel=?, adresse=?, date_naiss=?, lieu_naiss=?, groupe_TD=?, groupe_TP=?, promotion=? WHERE num_etudiant=?";
    $pdo->prepare($sql)->execute([
        $_POST['nom'], $_POST['prenom'], $_POST['tel'], $_POST['adresse'], 
        $_POST['date_naiss'], $_POST['lieu_naiss'], $_POST['groupe_td'], $_POST['groupe_tp'], $_POST['promotion'], $id
    ]);
    $success = true;
}

$user = $pdo->prepare("SELECT * FROM Etudiant WHERE num_etudiant = ?");
$user->execute([$id]);
$u = $user->fetch();
?>

<link rel="stylesheet" href="../../assets/css/style.css">
<div class="container py-5" style="max-width: 1200px;">
    <?php if($success): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3">✨ Profil mis à jour avec succès !</div>
    <?php endif; ?>

    <div class="row g-5">
        <div class="col-lg-4">
            <div class="card border-0 shadow p-5 text-center bg-white h-100 d-flex flex-column align-items-center justify-content-start" style="border-radius: 24px; min-height: 500px;">
                <div class="my-4 text-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="110" height="110" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                    </svg>
                </div>
                
                <h2 class="fw-bold text-dark mb-5" style="font-size: 2rem; letter-spacing: -0.5px;">
                    <?= htmlspecialchars($u['prenom']) ?> <span class="text-uppercase"><?= htmlspecialchars($u['nom']) ?></span>
                </h2>
                
                <div class="text-start w-100 mt-auto ps-2 text-secondary" style="font-size: 0.95rem; line-height: 1.6;">
                    <p class="m-0"><strong>N°:</strong> <?= htmlspecialchars($u['num_etudiant']) ?></p>
                    <p class="m-0"><strong>Email:</strong> <?= htmlspecialchars($u['identifiant']) ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow p-5 bg-white" style="border-radius: 24px;">
                <h3 class="fw-bold text-center mb-5 text-dark" style="font-size: 1.9rem; letter-spacing: -0.5px;">Modifier mes informations</h3>
                
                <form method="POST" class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Nom</label>
                        <input type="text" name="nom" class="form-control py-2 px-3 border" style="border-radius: 8px;" value="<?= htmlspecialchars($u['nom']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Prénom</label>
                        <input type="text" name="prenom" class="form-control py-2 px-3 border" style="border-radius: 8px;" value="<?= htmlspecialchars($u['prenom']) ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Téléphone</label>
                        <input type="text" name="tel" class="form-control py-2 px-3 border" style="border-radius: 8px;" value="<?= htmlspecialchars($u['tel']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Promotion actuelle</label>
                        <select name="promotion" id="promoDash" class="form-select py-2 px-3 border" style="border-radius: 8px;" onchange="updateGroups('promoDash', 'tdDash', 'tpDash')">
                            <option value="MMI1" <?= $u['promotion'] == 'MMI1' ? 'selected' : '' ?>>MMI1</option>
                            <option value="MMI2" <?= $u['promotion'] == 'MMI2' ? 'selected' : '' ?>>MMI2</option>
                            <option value="MMI3" <?= $u['promotion'] == 'MMI3' ? 'selected' : '' ?>>MMI3</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Groupe TD</label>
                        <select name="groupe_td" id="tdDash" class="form-select py-2 px-3 border" style="border-radius: 8px;"></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Groupe TP</label>
                        <select name="groupe_tp" id="tpDash" class="form-select py-2 px-3 border" style="border-radius: 8px;"></select>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark">Adresse</label>
                        <input type="text" name="adresse" class="form-control py-2 px-3 border" style="border-radius: 8px;" value="<?= htmlspecialchars($u['adresse']) ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Né(e) le</label>
                        <input type="date" name="date_naiss" class="form-control py-2 px-3 border" style="border-radius: 8px;" value="<?= htmlspecialchars($u['date_naiss']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Lieu de naissance</label>
                        <input type="text" name="lieu_naiss" class="form-control py-2 px-3 border" style="border-radius: 8px;" value="<?= htmlspecialchars($u['lieu_naiss']) ?>">
                    </div>

                    <div class="col-12 mt-5">
                        <button type="submit" class="btn w-100 py-3 fw-bold text-uppercase text-white shadow-sm" 
                                style="background-color: #2E4588 !important; border: none; border-radius: 8px; font-size: 0.85rem; letter-spacing: 0.5px;">
                            Sauvegarder les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateGroups(promoId, tdId, tpId, currentTD = "", currentTP = "") {
    const promo = document.getElementById(promoId).value;
    const td = document.getElementById(tdId);
    const tp = document.getElementById(tpId);
    let optionsTD = ""; let optionsTP = "";

    if(promo === "MMI1") {
        optionsTD = ["TD1","TD2","TD3"];
        optionsTP = ["TPA","TPB","TPC","TPD","TPE","TPF"];
    } else {
        optionsTD = ["TD1","TD2"];
        optionsTP = ["TPA","TPB","TPC","TPD"];
    }

    td.innerHTML = optionsTD.map(g => `<option value="${g}" ${g === currentTD ? 'selected' : ''}>${g}</option>`).join('');
    tp.innerHTML = optionsTP.map(g => `<option value="${g}" ${g === currentTP ? 'selected' : ''}>${g}</option>`).join('');
}

// Initialisation dynamique basée sur la BDD
updateGroups('promoDash', 'tdDash', 'tpDash', '<?= $u['groupe_TD'] ?>', '<?= $u['groupe_TP'] ?>');
</script>

<?php include '../../includes/footer.php'; ?>