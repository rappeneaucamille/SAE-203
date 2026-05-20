<?php 
require_once '../includes/db.php';
include '../includes/header.php';
if ($_SESSION['role'] !== 'Responsable stage') header('Location: ../index.php');
?>
<div class="container">
    <h2 class="mb-4">Espace Responsable Stages</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card p-4 mb-4">
                <h5>Saisir une nouvelle offre</h5>
                <form method="POST" action="traitement_offre.php">
                    <div class="mb-3"><label>Intitulé</label><input type="text" name="intitule" class="form-control" required></div>
                    <div class="mb-3"><label>Lieu</label><input type="text" name="lieu" class="form-control" required></div>
                    <div class="mb-3"><label>Description</label><textarea name="desc" class="form-control"></textarea></div>
                    <button class="btn btn-primary">Publier l'offre</button>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4">
                <h5>Étudiants en attente de validation</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Jean Dupont - Stage Web
                        <button class="btn btn-sm btn-success">Valider</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>