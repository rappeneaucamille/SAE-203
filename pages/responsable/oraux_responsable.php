<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité : Seul le Responsable stage peut accéder à cette page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Responsable stage') {
    header('Location: ../../index.php');
    exit();
}

$status = null;
$error = null;

// 1. RÉCUPÉRATION DES JURYS DEPUIS LA BDD (Selon ton ENUM exact)
try {
    $stmt = $pdo->prepare("SELECT identifiant, nom, prenom FROM enseignant WHERE fonctions = 'Jury de soutenance' ORDER BY nom ASC");
    $stmt->execute();
    $liste_jurys = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des jurys : " . $e->getMessage();
}

// 2. TRAITEMENT DU BOUTON "PROGRAMMER LA SOUTENANCE"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['programmer'])) {
    $etudiant_email = $_POST['etudiant_email']; // Identifiant (email) de l'étudiant
    $date_soutenance = $_POST['date_soutenance'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $salle = trim($_POST['salle']);
    $jury1 = $_POST['jury1']; // identifiant du prof 1
    $jury2 = $_POST['jury2']; // identifiant du prof 2

    if ($jury1 === $jury2) {
        $error = "Le premier et le deuxième membre du jury doivent être des enseignants différents.";
    } else {
        try {
            $pdo->beginTransaction();

            // A. Insertion du binôme dans la table `jury`
            $sqlJury = "INSERT INTO jury (enseignant_1, enseignant_2) VALUES (?, ?)";
            $stmtJury = $pdo->prepare($sqlJury);
            $stmtJury->execute([$jury1, $jury2]);
            
            // On récupère l'id_jury qui vient d'être créé automatiquement
            $id_jury_genere = $pdo->lastInsertId();

            // B. Insertion de la soutenance liée à cet id_jury
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

// Récupération des étudiants (on utilise l'identifiant pour la table soutenance)
$etudiants = $pdo->query("SELECT identifiant, nom, prenom FROM etudiant ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="card p-4 mx-auto shadow border-0" style="max-width: 600px;">
        <h4 class="fw-bold mb-4" style="color: #0055A4;">Programmer une Soutenance</h4>

        <?php if ($status === 'success'): ?>
            <div class="alert alert-success border-0 shadow-sm mb-3">La soutenance a été programmée avec succès !</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger border-0 shadow-sm mb-3"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Étudiant concerné</label>
                <select name="etudiant_email" class="form-select" required>
                    <option value="">-- Sélectionner un étudiant --</option>
                    <?php foreach ($etudiants as $etud): ?>
                        <option value="<?= htmlspecialchars($etud['identifiant']) ?>">
                            <?= strtoupper(htmlspecialchars($etud['nom'])) ?> <?= htmlspecialchars($etud['prenom']) ?> (<?= htmlspecialchars($etud['identifiant']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Date de la soutenance</label>
                <input type="date" name="date_soutenance" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Heure de début</label>
                    <input type="time" name="heure_debut" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Heure de fin</label>
                    <input type="time" name="heure_fin" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Salle</label>
                <input type="text" name="salle" class="form-control" placeholder="Ex: B105" required>
            </div>

            <hr class="my-4">
            <h5 class="fw-bold mb-3 text-secondary">Sélection des Jurys</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Membre Jury 1</label>
                    <select name="jury1" class="form-select" required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($liste_jurys as $jury): ?>
                            <option value="<?= htmlspecialchars($jury['identifiant']) ?>">
                                <?= strtoupper(htmlspecialchars($jury['nom'])) ?> <?= htmlspecialchars($jury['prenom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Membre Jury 2</label>
                    <select name="jury2" class="form-select" required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($liste_jurys as $jury): ?>
                            <option value="<?= htmlspecialchars($jury['identifiant']) ?>">
                                <?= strtoupper(htmlspecialchars($jury['nom'])) ?> <?= htmlspecialchars($jury['prenom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" name="programmer" class="btn btn-primary w-100 py-2 fw-bold">
                    <i class="bi bi-calendar-plus"></i> Programmer la soutenance
                </button>
                <a href="dashboard.php" class="btn btn-light border w-100 py-2">Retour Dashboard</a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>