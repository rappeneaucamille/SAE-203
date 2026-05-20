<?php 
require_once '../includes/db.php';
include '../includes/header.php';
// Seul le Chef de département ou Admin accède ici
if (!in_array($_SESSION['role'], ['Chef de département', 'Administrateur'])) header('Location: ../index.php');
?>

<div class="container">
    <h2 class="mb-4">Administration Globale</h2>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card p-3">
                <h5>Gestion des Étudiants</h5>
                <table class="table table-sm">
                    <thead><tr><th>Nom</th><th>Promo</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php 
                        $res = $pdo->query("SELECT * FROM Etudiant LIMIT 10");
                        while($e = $res->fetch()): ?>
                        <tr><td><?= $e['nom'] ?></td><td><?= $e['promotion'] ?></td>
                        <td><button class="btn btn-danger btn-sm">Supprimer</button></td></tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card p-3">
                <h5>Corps Enseignant</h5>
                <table class="table table-sm">
                    <thead><tr><th>Nom</th><th>Rôle</th></tr></thead>
                    <tbody>
                        <?php 
                        $res = $pdo->query("SELECT * FROM Enseignant");
                        while($prof = $res->fetch()): ?>
                        <tr><td><?= $prof['nom'] ?></td><td><span class="badge bg-info"><?= $prof['fonctions'] ?></span></td></tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>