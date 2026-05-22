<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// On récupère les notes dans la table Soutenance liée à l'étudiant
$stmt = $pdo->prepare("
    SELECT note_rapport, note_soutenance, observation_jury 
    FROM Soutenance s
    JOIN Stage st ON s.id_soutenance = st.id_soutenance
    WHERE st.num_etudiant = ?
");
$stmt->execute([$_SESSION['user_id']]);
$notes = $stmt->fetch();

$moyenne = ($notes) ? ($notes['note_rapport'] + $notes['note_soutenance']) / 2 : null;
?>

<div class="container text-center">
    <h2 class="fw-bold mb-5 text-start">Mes Résultats</h2>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card p-4 shadow">
                <h6 class="text-muted text-uppercase">Rapport de stage</h6>
                <h1 class="display-4 fw-bold text-primary"><?= $notes['note_rapport'] ?? '--' ?></h1>
                <small>/ 20</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 shadow">
                <h6 class="text-muted text-uppercase">Soutenance Orale</h6>
                <h1 class="display-4 fw-bold text-primary"><?= $notes['note_soutenance'] ?? '--' ?></h1>
                <small>/ 20</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 shadow bg-dark text-white">
                <h6 class="text-uppercase">Moyenne Finale</h6>
                <h1 class="display-4 fw-bold text-success"><?= $moyenne ?? '--' ?></h1>
                <small>/ 20</small>
            </div>
        </div>
    </div>

    <?php if($notes && $notes['observation_jury']): ?>
        <div class="card p-4 mt-4 text-start">
            <h5>Commentaires du jury :</h5>
            <p class="fst-italic text-muted">" <?= $notes['observation_jury'] ?> "</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>