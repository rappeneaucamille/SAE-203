<?php
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/引入/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: #0055A4 !important;">    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/index.php">
            <img src="../../assets/img/logo_univ.png" alt="Univ" height="40" class="me-2 bg-white rounded p-1" onerror="this.src='/assets/img/logo_univ.png'">
            <img src="../../assets/img/logo_site.png" alt="Site" height="40" class="me-2 bg-white rounded p-1" onerror="this.src='/assets/img/logo_site.png'">
            <span class="fw-bold ms-2">MMI STAGES</span>
        </a>

        

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (isset($_SESSION['role'])): ?>
                    
                    <?php if ($_SESSION['role'] == 'etudiant'): ?>
                        <li class="nav-item"><a class="nav-link" href="/pages/etudiant/dashboard.php">Profil</a></li>
                        <li class="nav-item"><a class="nav-link" href="/pages/etudiant/recherche.php">Recherche</a></li>
                        <li class="nav-item"><a class="nav-link" href="/pages/etudiant/mon_stage.php">Mon Stage</a></li>

                    <?php elseif ($_SESSION['role'] == 'Responsable stage'): ?>
                        <li class="nav-item"><a class="nav-link" href="/pages/responsable/dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/pages/responsable/validation.php">Validation</a></li>
                        <li class="nav-item"><a class="nav-link" href="/pages/responsable/suivi.php">Suivi</a></li>

                    <?php elseif ($_SESSION['role'] == 'Administrateur'): ?>
                        <li class="nav-item"><a class="nav-link fw-bold" href="/pages/admin/gestion.php">ADMIN</a></li>

                    <?php elseif ($_SESSION['role'] == 'Chef de département'): ?>
                        <li class="nav-item"><a class="nav-link" href="/pages/chef_dept/dashboard.php">Vision Globale</a></li>
                    
                    <?php elseif ($_SESSION['role'] == 'Jury de soutenance'): ?>
                        <li class="nav-item"><a class="nav-link" href="/pages/jury/notes.php">Saisie Notes</a></li>
                    <?php endif; ?>

                    <li class="nav-item ms-lg-4">
                        <a class="btn btn-light btn-sm fw-bold btn-confirm" 
                           style="color: var(--mmi-blue);" 
                           data-confirm="Voulez-vous vraiment vous déconnecter ?" 
                           href="../../auth/deconnexion.php">
                           Se déconnecter
                        </a>
                    </li>

                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/index.php">Connexion</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container content-wrapper">