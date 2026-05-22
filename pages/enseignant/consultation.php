<?php 
require_once '../../includes/db.php';
include '../../includes/header.php';
if ($_SESSION['role'] !== 'Enseignant standard') header('Location: ../../index.php');
?>
<div class="container">
    <h2 class="mb-4">Consultation des Étudiants</h2>
    <div class="card p-4">
        <div class="mb-3">
            <input type="text" id="tableSearch" class="form-control" placeholder="Rechercher un nom, une promo ou une entreprise...">
        </div>
        <table class="table table-striped">
            <thead>
                <tr><th>Nom</th><th>Promotion</th><th>Statut Stage</th></tr>
            </thead>
            <tbody>
                <tr><td>MARTIN Alice</td><td>MMI2</td><td><span class="badge bg-success">Affectée</span></td></tr>
            </tbody>
        </table>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>