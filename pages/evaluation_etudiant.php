<?php 
require_once '../includes/db.php';
include '../includes/header.php';
?>
<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Évaluation & Résultats</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card text-center p-4 shadow-sm">
                <h6 class="text-muted">Note Rapport</h6>
                <h2 style="color:var(--bleu-marine)">-- / 20</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-4 shadow-sm">
                <h6 class="text-muted">Note Soutenance</h6>
                <h2 style="color:var(--bleu-marine)">-- / 20</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-4 shadow-sm">
                <h6 class="text-muted">Moyenne Finale</h6>
                <h2 class="text-success">-- / 20</h2>
            </div>
        </div>
        <div class="col-12">
            <div class="card p-4">
                <h5>Suivi pédagogique</h5><hr>
                <p><strong>Date de visite prévue :</strong> 15 Juin 2026</p>
                <p><strong>Tuteur enseignant :</strong> M. BACCOUCH</p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>