<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : On autorise le Responsable OU l'Admin
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Responsable stage' && $_SESSION['role'] !== 'Administrateur')) {
    header('Location: ../../index.php');
    exit();
}

$status = null;
$error = null;

// 1. RÉCUPÉRATION DES JURYS DEPUIS LA BDD
try {
    $stmt = $pdo->prepare("SELECT identifiant, nom, prenom FROM enseignant WHERE fonctions = 'Jury de soutenance' ORDER BY nom ASC");
    $stmt->execute();
    $liste_jurys = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des jurys : " . $e->getMessage();
}

// 2. TRAITEMENT DU BOUTON "PROGRAMMER LA SOUTENANCE"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['programmer'])) {
    $etudiant_email = $_POST['etudiant_email']; 
    $date_soutenance = $_POST['date_soutenance'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $salle = trim($_POST['salle']);
    $jury1 = $_POST['jury1']; 
    $jury2 = $_POST['jury2']; 

    if ($jury1 === $jury2) {
        $error = "Le premier et le deuxième membre du jury doivent être des enseignants différents.";
    } else {
        try {
            $pdo->beginTransaction();

            $sqlJury = "INSERT INTO jury (enseignant_1, enseignant_2) VALUES (?, ?)";
            $stmtJury = $pdo->prepare($sqlJury);
            $stmtJury->execute([$jury1, $jury2]);
            
            $id_jury_genere = $pdo->lastInsertId();

            $sqlSoutenance = "INSERT INTO soutenance (date_soutenance, heure_debut, heure_fin, etudiant, salle, id_jury) 
                              VALUES (?, ?, ?, ?, ?, ?)";
            $stmtSoutenance = $pdo->prepare($sqlSoutenance);
            $stmtSoutenance->execute([$date_soutenance, $heure_debut, $heure_fin, $etudiant_email, $salle, $id_jury_genere]);

            $pdo->commit();
            $status = "success";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Impossible de planifier la soutenance : " . $e->getMessage();
        }
    }
}

// 3. RÉCUPÉRATION DES ÉTUDIANTS
$etudiants = $pdo->query("SELECT identifiant, nom, prenom FROM etudiant ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5" style="max-width: 1000px;">
    
    <h1 class="fw-bold mb-5 d-flex align-items-center gap-3" style="color: #000000; font-size: 2.2rem; letter-spacing: -0.5px;">
        <i class="bi bi-calendar4-week text-dark"></i> Organisation des Oraux
    </h1>

    <?php if ($status === 'success'): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">✅ La soutenance a été programmée avec succès !</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="bg-white p-5 border-0 position-relative" 
             style="border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.05), 0 3px 10px rgba(0,0,0,0.015) !important;">
            
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h3 class="fw-bold m-0" style="color: #000000; font-size: 1.6rem; letter-spacing: -0.3px;">Planifier un passage</h3>
                
                <button type="submit" name="programmer" class="btn btn-outline-success fw-medium px-4" 
                        style="border-color: #76BA99; color: #4E9F75; border-radius: 6px; font-size: 0.9rem; height: 40px;">
                    Valider cette soutenance
                </button>
            </div>

            <div class="row g-4">
                
                <div class="col-md-5">
                    <div class="p-3 border-0 h-100 d-flex flex-column justify-content-center" style="background-color: #F8FAFC; border-radius: 16px;">
                        <label class="form-label fw-bold text-dark text-center w-100 mb-2" style="font-size: 0.95rem;">Étudiant (ayant un stage validé)</label>
                        <select name="etudiant_email" class="form-select bg-white text-secondary text-center" style="border-radius: 8px; height: 43px; font-size: 0.9rem;" required>
                            <option value="">-- Sélectionnez l'étudiant --</option>
                            <?php foreach ($etudiants as $etud): ?>
                                <option value="<?= htmlspecialchars($etud['identifiant']) ?>">
                                    <?= strtoupper(htmlspecialchars($etud['nom'])) ?> <?= htmlspecialchars($etud['prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 border-0 h-100 d-flex flex-column justify-content-center" style="background-color: #F8FAFC; border-radius: 16px;">
                        <label class="form-label fw-bold text-dark text-center w-100 mb-2" style="font-size: 0.95rem;">Date et Heure</label>
                        <div class="d-flex flex-column gap-2">
                            <input type="date" name="date_soutenance" class="form-control bg-white text-secondary text-center" style="border-radius: 8px; height: 40px; font-size: 0.9rem;" required>
                            <div class="d-flex gap-2">
                                <input type="time" name="heure_debut" class="form-control bg-white text-secondary text-center" title="Heure de début" style="border-radius: 8px; height: 35px; font-size: 0.85rem;" required>
                                <input type="time" name="heure_fin" class="form-control bg-white text-secondary text-center" title="Heure de fin" style="border-radius: 8px; height: 35px; font-size: 0.85rem;" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 border-0 h-100 d-flex flex-column justify-content-center" style="background-color: #F8FAFC; border-radius: 16px;">
                        <label class="form-label fw-bold text-dark text-center w-100 mb-2" style="font-size: 0.95rem;">Salle</label>
                        <input type="text" name="salle" class="form-control bg-white text-secondary text-center" placeholder="Ex : B102" style="border-radius: 8px; height: 43px; font-size: 0.9rem;" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 border-0" style="background-color: #F8FAFC; border-radius: 16px;">
                        <label class="form-label fw-bold text-dark text-center w-100 mb-2" style="font-size: 0.95rem;">Jury</label>
                        <select name="jury1" class="form-select bg-white text-secondary text-center" style="border-radius: 8px; height: 43px; font-size: 0.9rem;" required>
                            <option value="">-- Sélectionnez le premier jury --</option>
                            <?php foreach ($liste_jurys as $jury): ?>
                                <option value="<?= htmlspecialchars($jury['identifiant']) ?>">
                                    <?= strtoupper(htmlspecialchars($jury['nom'])) ?> <?= htmlspecialchars($jury['prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 border-0" style="background-color: #F8FAFC; border-radius: 16px;">
                        <label class="form-label fw-bold text-dark text-center w-100 mb-2" style="font-size: 0.95rem;">Jury</label>
                        <select name="jury2" class="form-select bg-white text-secondary text-center" style="border-radius: 8px; height: 43px; font-size: 0.9rem;" required>
                            <option value="">-- Sélectionnez le second jury --</option>
                            <?php foreach ($liste_jurys as $jury): ?>
                                <option value="<?= htmlspecialchars($jury['identifiant']) ?>">
                                    <?= strtoupper(htmlspecialchars($jury['nom'])) ?> <?= htmlspecialchars($jury['prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

            </div>

            <div class="mt-5 pt-2 text-start">
                <a href="dashboard.php" class="text-secondary fw-medium text-decoration-none small d-inline-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Retour Dashboard
                </a>
            </div>

        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>