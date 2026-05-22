/**
 * Script de gestion pour la plateforme MMI Stages
 * Focus : Expérience Utilisateur (UX) et Scannabilité
 */

document.addEventListener('DOMContentLoaded', () => {
    console.log("Plateforme MMI Stages chargée !");

    // --- 1. GESTION DES ALERTES (AUTO-FERMETURE) ---
    // Les messages de succès ou d'erreur disparaissent seuls après 5 secondes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // --- 2. CONFIRMATIONS D'ACTIONS CRITIQUES ---
    // Pour éviter de valider ou supprimer un stage par erreur
    const confirmButtons = document.querySelectorAll('.btn-confirm');
    confirmButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const message = btn.getAttribute('data-confirm') || "Êtes-vous sûr de vouloir effectuer cette action ?";
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // --- 3. FILTRE DYNAMIQUE (CÔTÉ CLIENT) ---
    // Utile pour la page recherche.php ou consultation.php
    // Permet de chercher un étudiant ou une offre sans recharger la page
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    // --- 4. INTERACTIVITÉ DES CARTES (HOVER JS) ---
    // On ajoute une petite classe "shadow" plus forte au survol pour le relief
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.classList.add('shadow');
        });
        card.addEventListener('mouseleave', () => {
            card.classList.remove('shadow');
        });
    });

    // --- 5. CALCULATEUR DE MOYENNE EN DIRECT (Page Jury) ---
    // Si tu es sur la page jury/notes.php, calcule la moyenne auto
    const noteRapport = document.querySelector('input[name="note_rapport"]');
    const noteSoutenance = document.querySelector('input[name="note_soutenance"]');
    
    if (noteRapport && noteSoutenance) {
        const updateMoyenne = () => {
            const r = parseFloat(noteRapport.value) || 0;
            const s = parseFloat(noteSoutenance.value) || 0;
            const moy = (r + s) / 2;
            console.log("Moyenne calculée : " + moy + "/20");
            // Optionnel : afficher la moyenne dans un petit badge
        };
        noteRapport.addEventListener('input', updateMoyenne);
        noteSoutenance.addEventListener('input', updateMoyenne);
    }
});