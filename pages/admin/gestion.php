<?php 
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité : Seul l'admin peut entrer ici
if ($_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// Récupérer tous les utilisateurs
$profs = $pdo->query("SELECT * FROM Enseignant ORDER BY nom ASC")->fetchAll();
$etudiants = $pdo->query("SELECT * FROM Etudiant ORDER BY nom ASC")->fetchAll();
?>

<div class="container">
    <h2 class="fw-bold mb-4">Administration du Système</h2>

    <div class="mb-3">
        <input type="text" id="tableSearch" class="form-control" placeholder="Rechercher un utilisateur (nom, email, rôle)...">
    </div>

    <div class="card p-4 shadow-sm mb-5">
        <h5><i class="bi bi-people"></i> Gestion des Enseignants & Staff</h5>
        <table class="table table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle actuel</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($profs as $p): ?>
                <tr>
                    <td><?= strtoupper($p['nom']) ?> <?= $p['prenom'] ?></td>
                    <td><?= $p['identifiant'] ?></td>
                    <td><span class="badge bg-info text-dark"><?= $p['fonctions'] ?></span></td>
                    <td>
                        <button class="btn btn-danger btn-sm btn-confirm" data-confirm="Supprimer ce compte définitivement ?">
                            Supprimer
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>s