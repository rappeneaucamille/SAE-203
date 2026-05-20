<?php 
require_once '../includes/db.php';
include '../includes/header.php';
?>
<div class="container">
    <h2 class="mb-4" style="color:var(--bleu-marine)">Suivi et Affectation</h2>
    
    <div class="card p-3 mb-4 bg-light border-0">
        <div class="row g-2">
            <div class="col-md-8"><input type="text" class="form-control" placeholder="Rechercher un étudiant..."></div>
            <div class="col-md-4"><select class="form-select"><option>Filtrer par année</option><option>2026</option></select></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card p-4 shadow-sm">
                <h5>Suivi des recherches (Entreprises contactées)</h5>
                <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item">Camille R. : 3 contacts (Ubisoft, Google, Inria)</li>
                </ul>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card p-4 border-primary">
                <h5>Affectation officielle</h5>
                <form class="mt-3">
                    <label class="small">Étudiant</label>
                    <select class="form-select mb-2"><option>Choisir...</option></select>
                    <label class="small">Stage validé</label>
                    <select class="form-select mb-3"><option>Choisir...</option></select>
                    <button class="btn btn-primary w-100">Confirmer l'affectation</button>
                </form>
            </div>
        </div>
    </div>
</div>