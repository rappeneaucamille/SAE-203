<?php 
require_once '../includes/db.php';
include '../includes/header.php';
?>
<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Mon Stage</h2>
    <div class="card p-4 mb-4">
        <div class="row">
            <div class="col-md-6">
                <h5>L'Entreprise</h5><hr>
                <p><strong>Nom :</strong> Non affecté pour le moment</p>
                <p><strong>Adresse :</strong> ...</p>
            </div>
            <div class="col-md-6">
                <h5>Maître de Stage</h5><hr>
                <p><strong>Nom :</strong> ...</p>
                <p><strong>Contact :</strong> ...</p>
            </div>
        </div>
    </div>
    <div class="card p-4 border-info">
        <h5>Convention de stage</h5><hr>
        <p><strong>Numéro de convention :</strong> NC-2026-XXXX</p>
        <p><strong>Statut :</strong> <span class="text-info">En attente de signature direction</span></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>