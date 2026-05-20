<?php 
require_once '../includes/db.php';
include '../includes/header.php';
?>
<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Validation des stages</h2>
    <div class="card shadow-sm p-4">
        <h5>Demandes en attente</h5>
        <table class="table table-hover">
            <thead>
                <tr><th>Étudiant</th><th>Entreprise</th><th>Missions</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Jean DUPONT</td>
                    <td>WebCorp</td>
                    <td>Développement PHP...</td>
                    <td>
                        <button class="btn btn-success btn-sm">Valider</button>
                        <button class="btn btn-danger btn-sm">Refuser</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>