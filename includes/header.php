<?php
// On vérifie si une session est déjà lancée, sinon on la lance
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <title>SAé MMI - Gestion des Stages</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm mb-4" style="background-color: var(--bleu-marine);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">MMI STAGES</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                
                <?php if (isset($_SESSION['role'])): ?>
                    
                    <?php if ($_SESSION['role'] == 'etudiant'): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard_etudiant.php">Profil</a></li>
                        <li class="nav-item"><a class="nav-link" href="recherche_etudiant.php">Recherche</a></li>
                        <li class="nav-item"><a class="nav-link" href="mon_stage.php">Mon Stage</a></li>
                        <li class="nav-item"><a class="nav-link" href="evaluation_etudiant.php">Évaluation</a></li>

                    <?php elseif ($_SESSION['role'] == 'Responsable stage'): ?>
                        <li class="nav-item"><a class="nav-link" href="validation_responsable.php">Validation</a></li>
                        <li class="nav-item"><a class="nav-link" href="offres_responsable.php">Offres</a></li>
                        <li class="nav-item"><a class="nav-link" href="suivi_responsable.php">Suivi & Affectation</a></li>
                        <li class="nav-item"><a class="nav-link" href="oraux_responsable.php">Oraux</a></li>
                        <li class="nav-item"><a class="nav-link" href="stats_responsable.php">Statistiques</a></li>
                    
                        <?php elseif ($_SESSION['role'] == 'Jury de soutenance'): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard_jury.php">Notes Oraux</a></li>

                    <?php elseif ($_SESSION['role'] == 'Enseignant standard'): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard_enseignant.php">Consultation</a></li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link text-warning fw-bold ms-lg-3" href="../deconnexion.php">Déconnexion</a>
                    </li>

                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="../index.php">Connexion</a></li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>