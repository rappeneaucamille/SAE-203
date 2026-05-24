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

<div class="container py-5">
    <?php if($success): ?>
        <div class="alert alert-success shadow-sm border-0 mb-4">✨ Profil mis à jour avec succès !</div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center p-4" style="background-color: var(--pastel-purple); border-radius: 20px;">
                <div class="mb-3"><i class="bi bi-person-badge" style="font-size: 5rem; color: var(--mmi-blue);"></i></div>
                <h3 class="fw-bold"><?= $u['prenom'] ?> <?= strtoupper($u['nom']) ?></h3>
                <span class="badge px-3 py-2 fs-6" style="background-color: var(--mmi-blue);"><?= $u['promotion'] ?></span>
                <div class="mt-4 text-start small">
                    <p class="mb-1"><strong>N° :</strong> <?= $u['num_etudiant'] ?></p>
                    <p class="mb-1"><strong>Email :</strong> <?= $u['identifiant'] ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                <h4 class="fw-bold mb-4" style="color: var(--mmi-blue);">Éditer mes informations</h4>
                <form method="POST" class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-bold">Nom</label><input type="text" name="nom" class="form-control shadow-sm" value="<?= $u['nom'] ?>"></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Prénom</label><input type="text" name="prenom" class="form-control shadow-sm" value="<?= $u['prenom'] ?>"></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Téléphone</label><input type="text" name="tel" class="form-control shadow-sm" value="<?= $u['tel'] ?>"></div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Promotion actuelle</label>
                        <select name="promotion" id="promoDash" class="form-select shadow-sm" onchange="updateGroups('promoDash', 'tdDash', 'tpDash')">
                            <option value="MMI1" <?= $u['promotion'] == 'MMI1' ? 'selected' : '' ?>>MMI 1</option>
                            <option value="MMI2" <?= $u['promotion'] == 'MMI2' ? 'selected' : '' ?>>MMI 2</option>
                            <option value="MMI3" <?= $u['promotion'] == 'MMI3' ? 'selected' : '' ?>>MMI 3</option>
                        </select>
                    </div>

                    <div class="col-md-6"><label class="form-label fw-bold">Groupe TD</label><select name="groupe_td" id="tdDash" class="form-select shadow-sm"></select></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Groupe TP</label><select name="groupe_tp" id="tpDash" class="form-select shadow-sm"></select></div>

                    <div class="col-12"><label class="form-label fw-bold">Adresse</label><input type="text" name="adresse" class="form-control shadow-sm" value="<?= $u['adresse'] ?>"></div>
                    
                    <div class="col-md-6"><label class="form-label fw-bold">Né(e) le</label><input type="date" name="date_naiss" class="form-control shadow-sm" value="<?= $u['date_naiss'] ?>"></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Lieu de naissance</label><input type="text" name="lieu_naiss" class="form-control shadow-sm" value="<?= $u['lieu_naiss'] ?>"></div>

                    <div class="col-12 mt-5">
                        <button type="submit" class="btn btn w-100 shadow" 
                                style="background-color: #A7C7E7 !important; color: #000000 !important;; border: 2px  #5D5D81; display: block;">
                            SAUVEGARDER LES MODIFICATIONS
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

// Initialisation avec les valeurs actuelles de la base
updateGroups('promoDash', 'tdDash', 'tpDash', '<?= $u['groupe_TD'] ?>', '<?= $u['groupe_TP'] ?>');
</script>
<?php include '../../includes/footer.php'; ?>