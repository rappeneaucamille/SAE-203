<?php 
require_once 'includes/db.php';
include 'includes/header.php';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    // 1. Test dans la table Etudiant
    $stmt = $pdo->prepare("SELECT * FROM Etudiant WHERE identifiant = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user['pwd'])) {
        $_SESSION['user_id'] = $user['num_etudiant'];
        $_SESSION['role'] = 'etudiant';
        header('Location: pages/dashboard_etudiant.php');
        exit();
    } else {
        // 2. Test dans la table Enseignant
        $stmt = $pdo->prepare("SELECT * FROM Enseignant WHERE identifiant = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($mdp, $user['pwd'])) {
            $_SESSION['user_id'] = $user['id_ens'];
            $_SESSION['role'] = $user['fonctions']; // Récupère la valeur de l'ENUM
            
            // Redirection selon la fonction précise définie à l'inscription
            if ($_SESSION['role'] == 'Responsable stage') {
                header('Location: pages/dashboard_responsable.php');
            } elseif ($_SESSION['role'] == 'Jury de soutenance') {
                header('Location: pages/dashboard_jury.php');
            } elseif ($_SESSION['role'] == 'Enseignant standard') {
                header('Location: pages/dashboard_enseignant.php');
            } elseif ($_SESSION['role'] == 'Chef de département') {
                header('Location: pages/dashboard_admin.php');
            } else {
                // Cas par défaut (Administrateur ou autre)
                header('Location: pages/dashboard_admin.php');
            }
            exit();
        } else { 
            $msg = "Identifiants incorrects."; 
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card p-4">
                <h3 class="text-center mb-4" style="color:var(--bleu-marine)">Portail MMI</h3>
                
                <?php if($msg): ?>
                    <div class="alert alert-danger"><?php echo $msg; ?></div>
                <?php endif; ?>

                <?php if(isset($_GET['success'])): ?>
                    <div class="alert alert-success">Inscription réussie, connectez-vous.</div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email universitaire / professionnel</label>
                        <input type="email" name="email" class="form-control" placeholder="nom@univ-eiffel.fr" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="mdp" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
                
                <hr>
                <div class="text-center mt-3">
                    <small>Pas encore de compte ?</small><br>
                    <a href="inscription.php" class="text-decoration-none">Inscription Étudiant</a> | 
                    <a href="inscription_enseignant.php" class="text-decoration-none">Espace Prof</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>