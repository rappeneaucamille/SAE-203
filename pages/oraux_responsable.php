<?php 
require_once '../includes/db.php';
include '../includes/header.php';
?>
<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Organisation des Oraux</h2>
    <div class="card p-4 shadow-sm">
        <form class="row g-3">
            <div class="col-md-4"><label>Date</label><input type="date" class="form-control"></div>
            <div class="col-md-4"><label>Heure</label><input type="time" class="form-control"></div>
            <div class="col-md-4"><label>Salle</label><input type="text" class="form-control" placeholder="Ex: A102"></div>
            <div class="col-md-6"><label>Membres du jury</label><input type="text" class="form-control" placeholder="Noms séparés par des virgules"></div>
            <div class="col-md-6"><label>Étudiant convoqué</label><select class="form-select"><option>Choisir...</option></select></div>
            <div class="col-12"><button class="btn btn-dark">Enregistrer la convocation</button></div>
        </form>
    </div>
</div>