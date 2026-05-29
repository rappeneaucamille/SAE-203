<?php
require_once 'includes/db.php';

// REDIRECTION AUTOMATIQUE SI DÉJÀ CONNECTÉ
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'etudiant':
            header('Location: pages/etudiant/recherche.php');
            break;
        case 'Responsable stage':
            header('Location: pages/responsable/dashboard.php');
            break;
        case 'Administrateur': // Ajout/Vérification ici
            header('Location: pages/admin/gestion.php');
            break;
        case 'Chef de département':
            header('Location: pages/chef_dept/dashboard.php');
            break;
        case 'Enseignant standard':
            header('Location: pages/enseignant/consultation.php');
            break;
        case 'Jury de soutenance':
            header('Location: pages/jury/notes.php');
            break;
        case 'Administrateur':
            header('Location: pages/admin/gestion.php');
            break;
    }
    exit();
}

include 'includes/header.php';
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card p-4 shadow-lg" style="max-width: 450px; width: 100%; border-top: 5px solid var(--mmi-blue);">
        <div class="text-center mb-4">
            <div class="d-flex justify-content-around align-items-center mb-3">
                <img src="assets/img/logo_univ.png" alt="Logo Université" style="height: 60px;">
                <img src="assets/img/logo_site.png" alt="Logo Site" style="height: 60px;">
            </div>
            <h2 class="fw-bold">MMI Meaux</h2>
            <p class="text-muted">Gestion des Stages • 3 ans de formation</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">Identifiants incorrects.</div>
        <?php endif; ?>

        <form action="auth/traitement_login.php" method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Email Universitaire</label>
                <input type="email" name="email" class="form-control" placeholder="nom.prenom@univ-eiffel.fr" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Mot de passe</label>
                <input type="password" name="mdp" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-mmi w-100 py-2">Se connecter</button>
        </form>
        
        <hr class="my-4">
        
        <div class="text-center">
            <p class="small mb-1">Pas encore de compte ?</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="auth/inscription.php" class="text-decoration-none fw-bold" style="color: var(--mmi-blue);">Étudiant</a>
                <span class="text-muted">|</span>
                <a href="auth/inscription_pro.php" class="text-decoration-none fw-bold" style="color: var(--dark-grey);">Enseignant / Admin</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>