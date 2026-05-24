<?php
require_once '../includes/db.php';
include '../includes/header.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $num = $_POST['num_etudiant'];
    $mdp = $_POST['mdp'];
    $mdp_conf = $_POST['mdp_conf'];

    if ($mdp !== $mdp_conf) {
        $message = "<div class='alert alert-danger shadow-sm'>Les mots de passe ne correspondent pas.</div>";
    } else {
        $check = $pdo->prepare("SELECT num_etudiant FROM Etudiant WHERE num_etudiant = ?");
        $check->execute([$num]);
        if ($check->fetch()) {
            $message = "<div class='alert alert-danger shadow-sm'>Ce numéro étudiant est déjà inscrit.</div>";
        } else {
            try {
                $hash = password_hash($mdp, PASSWORD_DEFAULT);
                $sql = "INSERT INTO Etudiant (num_etudiant, identifiant, pwd, nom, prenom, tel, date_naiss, lieu_naiss, adresse, promotion, groupe_TD, groupe_TP) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $num, $_POST['email'], $hash, strtoupper($_POST['nom']), $_POST['prenom'], 
                    $_POST['tel'], $_POST['date_naiss'], $_POST['lieu_naiss'], $_POST['adresse'],
                    $_POST['promotion'], $_POST['groupe_td'], $_POST['groupe_tp']
                ]);
                $message = "<div class='alert alert-success shadow-sm'>Inscription réussie ! <a href='../index.php' class='fw-bold'>Connectez-vous</a></div>";
            } catch(Exception $e) {
                $message = "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
            }
        }
    }
}
?>

<div class="container py-5">
    <div class="card mx-auto shadow-lg border-0" style="max-width: 850px; border-radius: 20px; overflow: hidden;">
        <div class="p-4 text-center" style="background-color: var(--mmi-blue);">
            <h2 class="fw-bold mb-1" style="color: #000000 !important;">Rejoindre MMI Stages</h2>
            <p style="color: #000000 !important; font-weight: 500;">Créez votre compte étudiant en quelques secondes</p>
        </div>

        <div class="card-body p-5" style="background-color: var(--pastel-blue);">
            <?= $message ?>
            <form method="POST" class="row g-4">
                <div class="col-md-4"><label class="form-label fw-bold">N° Étudiant</label><input type="number" name="num_etudiant" class="form-control shadow-sm" required></div>
                <div class="col-md-4"><label class="form-label fw-bold">Nom</label><input type="text" name="nom" class="form-control shadow-sm" required></div>
                <div class="col-md-4"><label class="form-label fw-bold">Prénom</label><input type="text" name="prenom" class="form-control shadow-sm" required></div>
                
                <div class="col-md-6"><label class="form-label fw-bold">Date de Naissance</label><input type="date" name="date_naiss" class="form-control shadow-sm" required></div>
                <div class="col-md-6"><label class="form-label fw-bold">Lieu de Naissance</label><input type="text" name="lieu_naiss" class="form-control shadow-sm" required></div>
                
                <div class="col-12"><label class="form-label fw-bold">Adresse Postale</label><input type="text" name="adresse" class="form-control shadow-sm" required></div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold">Promotion</label>
                    <select name="promotion" id="promoInscr" class="form-select shadow-sm" onchange="updateGroups('promoInscr', 'tdInscr', 'tpInscr')">
                        <option value="MMI1">MMI 1</option>
                        <option value="MMI2">MMI 2</option>
                        <option value="MMI3">MMI 3</option>
                    </select>
                </div>
                <div class="col-md-4"><label class="form-label fw-bold">Groupe TD</label><select name="groupe_td" id="tdInscr" class="form-select shadow-sm"></select></div>
                <div class="col-md-4"><label class="form-label fw-bold">Groupe TP</label><select name="groupe_tp" id="tpInscr" class="form-select shadow-sm"></select></div>

                <div class="col-md-6"><label class="form-label fw-bold">Email</label><input type="email" name="email" class="form-control shadow-sm" required></div>
                <div class="col-md-6"><label class="form-label fw-bold">Téléphone</label><input type="tel" name="tel" class="form-control shadow-sm"></div>

                <div class="col-md-6"><label class="form-label fw-bold">Mot de passe</label><input type="password" name="mdp" class="form-control shadow-sm" required></div>
                <div class="col-md-6"><label class="form-label fw-bold">Confirmation</label><input type="password" name="mdp_conf" class="form-control shadow-sm" required></div>

                <div class="col-12 mt-5 mb-3 text-center">
                    <button type="submit" class="btn btn w-100 shadow" 
                            style="background-color: #A7C7E7 !important; color: #000000 !important; border: 2px  #0055A4; display: block;">
                        FINALISER MON INSCRIPTION
                    </button>

                    <p class="mt-3">Déjà inscrit ? <a href="../index.php" style="color: #000000; font-weight: bold;">Se connecter</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateGroups(promoId, tdId, tpId) {
    const promo = document.getElementById(promoId).value;
    const td = document.getElementById(tdId);
    const tp = document.getElementById(tpId);
    
    let optionsTD = ""; let optionsTP = "";

    if(promo === "MMI1") {
        optionsTD = "<option>A1</option><option>A2</option><option>B1</option><option>B2</option><option>C1</option><option>C2</option>";
        optionsTP = "<option>TP1</option><option>TP2</option><option>TP3</option><option>TP4</option><option>TP5</option><option>TP6</option>";
    } else {
        optionsTD = "<option>A1</option><option>A2</option><option>B1</option><option>B2</option>";
        optionsTP = "<option>TP1</option><option>TP2</option><option>TP3</option><option>TP4</option>";
    }
    td.innerHTML = optionsTD; tp.innerHTML = optionsTP;
}
updateGroups('promoInscr', 'tdInscr', 'tpInscr');
</script>
<?php include '../includes/footer.php'; ?>