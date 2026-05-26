<?php
require_once '../../includes/db.php';
include '../../includes/header.php';

// SÉCURITÉ : On autorise le Responsable OU l'Admin
if ($_SESSION['role'] !== 'Responsable stage' && $_SESSION['role'] !== 'Administrateur') {
    header('Location: ../../index.php');
    exit();
}


if (isset($_POST['update_suivi'])) {
    // On utilise ta colonne "probleme" existante
    $stmt = $pdo->prepare("UPDATE stage SET probleme = ?, convention_signee = ? WHERE id_stage = ?");
    $stmt->execute([$_POST['refomulation'], $_POST['convention'], $_POST['id_stage']]);
    echo "<div class='alert alert-success m-2 shadow-sm'>Modifications enregistrées avec succès.</div>";
}
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color: #2e4588;"><i class="bi bi- clipboard-check"></i> Suivi & Remontée des Problèmes</h2>
    
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Étudiant</th>
                        <th>Entreprise</th>
                        <th>Signalement Étudiant</th>
                        <th>Note du Responsable (Attribut "probleme")</th>
                        <th>Convention</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT s.id_stage, s.lieu, s.convention_signee, s.probleme, s.alerte_etudiant, e.nom, e.prenom 
                            FROM stage s 
                            JOIN etudiant e ON s.num_etudiant = e.num_etudiant";
                    $res = $pdo->query($sql)->fetchAll();
                    
                    foreach($res as $row): ?>
                    <form method="POST">
                        <input type="hidden" name="id_stage" value="<?= $row['id_stage'] ?>">
                        <tr>
                            <td><strong><?= strtoupper($row['nom']) ?></strong> <?= $row['prenom'] ?></td>
                            <td><small><?= htmlspecialchars($row['lieu']) ?></small></td>
                            <td style="max-width: 200px;">
                                <?php if(!empty($row['alerte_etudiant'])): ?>
                                    <div class="p-2 border rounded bg-light small text-danger" style="font-style: italic;">
                                        "<?= htmlspecialchars($row['alerte_etudiant']) ?>"
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">RAS</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <textarea name="refomulation" class="form-control form-control-sm" rows="2" placeholder="Reformuler le problème ici..."><?= htmlspecialchars($row['probleme'] ?? '') ?></textarea>
                            </td>
                            <td>
                                <select name="convention" class="form-select form-select-sm">
                                    <option value="oui" <?= $row['convention_signee'] == 'oui' ? 'selected' : '' ?>>Signée</option>
                                    <option value="non" <?= $row['convention_signee'] == 'non' ? 'selected' : '' ?>>En attente</option>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="update_suivi" class="btn btn-sm btn-primary">Enregistrer</button>
                            </td>
                        </tr>
                    </form>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>