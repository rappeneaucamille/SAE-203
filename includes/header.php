<?php
// On démarre la session pour pouvoir lire le rôle de l'utilisateur
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMI Meaux - Gestion des Stages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: #0055A4 !important;">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/index.php">
            <img src="/SAE-203 - v2/assets/img/logo_univ.png" alt="Univ" height="40" class="me-2 rounded p-1">
            <img src="/SAE-203 - v2/assets/img/logo_site.png" alt="Site" height="40" class="me-2 rounded p-1">
            <span class="fw-bold ms-2">MMI STAGES</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (isset($_SESSION['role'])): ?>
                    
                    <?php if ($_SESSION['role'] == 'etudiant'): ?>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/etudiant/dashboard.php">Profil</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/etudiant/recherche.php">Recherche</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/etudiant/mon_stage.php">Mon Stage</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/etudiant/evaluation.php">Mes Evaluations</a></li>

                    <?php elseif ($_SESSION['role'] == 'Responsable stage'): ?>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/responsable/dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/responsable/offres.php">Offres</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/responsable/validation.php">Validation</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/responsable/suivi_responsable.php">Suivi</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/responsable/oraux_responsable.php">Soutenances</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/responsable/stats.php">Stats</a></li>
                        
                    <?php elseif ($_SESSION['role'] == 'Administrateur'): ?>
                        <li class="nav-item">
                            <a class="nav-link fw-bold text-warning" href="/SAE-203 - v2/pages/admin/gestion.php">
                                <i class="bi bi-shield-lock"></i> ADMIN
                            </a>
                        </li>
                        <li class="nav-item d-none d-lg-block"><span class="nav-link disabled text-white-50">|</span></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/responsable/dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/responsable/offres.php">Offres</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/responsable/validation.php">Validation</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/responsable/suivi_responsable.php">Suivi</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/responsable/oraux_responsable.php">Soutenances</a></li>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/responsable/stats.php">Stats</a></li>
                    
                    <?php elseif ($_SESSION['role'] == 'Enseignant standard'): ?>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/enseignant/consultation.php">Consultation</a></li>

                    <?php elseif ($_SESSION['role'] == 'Chef de département'): ?>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/chef_dept/dashboard.php">Vision Globale</a></li>
                    
                    <?php elseif ($_SESSION['role'] == 'Jury de soutenance'): ?>
                        <li class="nav-item"><a class="nav-link" href="/SAE-203 - v2/pages/jury/notes.php">Saisie Notes</a></li>

                    <?php endif; ?>

                    <li class="nav-item ms-lg-4">
                        <a class="btn btn-light btn-sm fw-bold" 
                           style="color: #0055A4;" 
                           onclick="return confirm('Voulez-vous vraiment vous déconnecter ?')"
                           href="/SAE-203 - v2/auth/deconnexion.php">
                             Se déconnecter
                        </a>
                    </li>

                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>