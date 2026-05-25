<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : Admin uniquement
if ($_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}

// Récupération de tous les stages validés qui ont des infos de maître de stage
$sql = "SELECT r.entreprise_contactee, r.reponses, e.nom as etud_nom, e.prenom as etud_prenom 
        FROM recherche r 
        JOIN effectuer ef ON r.id_recherche = ef.id_recherche 
        JOIN etudiant e ON ef.num_etudiant = e.num_etudiant
        WHERE r.statut = 'Validée' AND r.reponses IS NOT NULL AND r.reponses != '0'
        ORDER BY r.entreprise_contactee ASC";

$stmt = $pdo->query($sql);
$maitres = $stmt->fetchAll();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-people-fill text-primary"></i> Gestion des Maîtres de Stage</h2>
        <span class="badge bg-primary px-3 py-2"><?= count($maitres) ?> Tuteurs enregistrés</span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Maître de Stage</th>
                        <th>Entreprise</th>
                        <th>Étudiant encadré</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($maitres)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Aucun maître de stage trouvé dans les dossiers validés.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($maitres as $m): 
                            // Extraction des infos du texte brut (reponses)
                            $infos = explode("\n", $m['reponses']);
                            $nom_tuteur = "Non spécifié";
                            $email_tuteur = "Pas d'email";
                            
                            foreach($infos as $ligne) {
                                if(strpos($ligne, 'NOM :') !== false) $nom_tuteur = trim(str_replace('NOM :', '', $ligne));
                                if(strpos($ligne, 'PRÉNOM :') !== false) $nom_tuteur .= " " . trim(str_replace('PRÉNOM :', '', $ligne));
                                if(strpos($ligne, 'EMAIL :') !== false) $email_tuteur = trim(str_replace('EMAIL :', '', $ligne));
                            }
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($nom_tuteur) ?></div>
                                <div class="small text-muted"><i class="bi bi-envelope"></i> <?= htmlspecialchars($email_tuteur) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark"><?= htmlspecialchars($m['entreprise_contactee']) ?></span>
                            </td>
                            <td>
                                <div class="small"><?= strtoupper($m['etud_nom']) ?> <?= $m['etud_prenom'] ?></div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning" onclick="alert('Ce tuteur n\'a pas encore de compte utilisateur. Les accès seront générés lors de l\'envoi de la convention.')">
                                    <i class="bi bi-key"></i> Réinitialiser PW
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: rgba(0, 85, 164, 0.03);
    }
</style>

<?php include '../../includes/footer.php'; ?>