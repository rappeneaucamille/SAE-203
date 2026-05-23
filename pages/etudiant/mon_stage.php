<?php
$num_etud = $_SESSION['user_id'];

// On vérifie d'abord s'il y a un stage officiel
$sqlStage = "SELECT * FROM Stage WHERE num_etudiant = ?";
$resStage = $pdo->prepare($sqlStage);
$resStage->execute([$num_etud]);
$monStage = $resStage->fetch();

// SI PAS DE STAGE : On regarde s'il y a une recherche validée
if (!$monStage) {
    $sqlSearch = "SELECT r.* FROM Recherche r 
                  JOIN Effectuer ef ON r.id_recherche = ef.id_recherche 
                  WHERE ef.num_etudiant = ? AND r.statut = 'Validée' LIMIT 1";
    $resSearch = $pdo->prepare($sqlSearch);
    $resSearch->execute([$num_etud]);
    $rechercheValide = $resSearch->fetch();
}
?>

<div class="container">
    <?php if ($monStage): ?>
        <div class="alert alert-success">Votre stage est officiellement enregistré !</div>
        <?php elseif (isset($rechercheValide)): ?>
        <div class="card card-pastel-purple p-4">
            <h4>🎉 Félicitations !</h4>
            <p>Votre démarche pour <strong><?= $rechercheValide['entreprise_contactee'] ?></strong> a été validée par le responsable.</p>
            <p>Le secrétariat va maintenant générer votre convention.</p>
        </div>
    <?php else: ?>
        <p>Vous n'avez pas encore de stage validé.</p>
    <?php endif; ?>
</div>