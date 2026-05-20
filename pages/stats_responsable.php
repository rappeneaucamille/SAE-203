<?php 
require_once '../includes/db.php';
include '../includes/header.php';
?>
<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Statistiques du Département</h2>
    <div class="row">
        <div class="col-md-12">
            <div class="card p-4 shadow-sm mb-4">
                <h5>Rapport d'état des recherches</h5>
                <hr>
                <p><strong>Total Étudiants :</strong> 85</p>
                <p><strong>Avec Stage :</strong> 42 (49%)</p>
                <p><strong>En recherche :</strong> 43 (51%)</p>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-success" style="width: 49%">49% affectés</div>
                </div>
            </div>
        </div>
    </div>
</div>