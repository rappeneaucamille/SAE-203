<?php 
require_once '../../includes/db.php';
include '../../includes/header.php';
if ($_SESSION['role'] !== 'Responsable stage') header('Location: ../../index.php');
?>
<div class="container">
    <h2 class="mb-4">Gestion des Offres</h2>
    <div class="card p-4 mb-4">
        <h5>Publier une nouvelle offre</h5>
        <form action="traitement_offre.php" method="POST" class="row g-3">
            <div class="col-md-6"><input type="text" name="titre" class="form-control" placeholder="Titre du stage"></div>
            <div class="col-md-6"><input type="text" name="ent" class="form-control" placeholder="Entreprise"></div>
            <div class="col-12"><textarea name="desc" class="form-control" placeholder="Description"></textarea></div>
            <div class="col-12"><button class="btn btn-primary">Publier</button></div>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>