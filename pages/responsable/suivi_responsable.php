<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// Sécurité : Seul le responsable peut accéder
if ($_SESSION['role'] !== 'Responsable stage') {
    header('Location: ../../index.php');
    exit();
}
?>

<div class="container">
    <h2 class="fw-bold mb-4" style="color: #0055A4;">Suivi des conventions</h2>
    
    <div class="card p-4 shadow-sm">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Cette page affiche l'état de signature des conventions basées sur les stages validés.
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Étudiant</th>
                        <th>Lieu du stage</th>
                        <th>État Convention</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Requête basée sur ton SQL : Table Stage et Etudiant
                    $sql = "SELECT e.nom, e.prenom, s.lieu, s.convention_signee 
                            FROM Stage s 
                            JOIN Etudiant e ON s.num_etudiant = e.num_etudiant";
                    
                    $res = $pdo->query($sql)->fetchAll();
                    
                    if (count($res) > 0):
                        foreach($res as $row): ?>
                            <tr>
                                <td><strong><?= strtoupper($row['nom']) ?></strong> <?= $row['prenom'] ?></td>
                                <td><?= htmlspecialchars($row['lieu']) ?></td>
                                <td>
                                    <?= $row['convention_signee'] == 'oui' 
                                        ? '<span class="badge bg-success">✅ Signée</span>' 
                                        : '<span class="badge bg-warning text-dark">❌ En attente</span>' ?>
                                </td>
                            </tr>
                        <?php endforeach; 
                    else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted p-4">Aucun stage n'a encore été officialisé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>