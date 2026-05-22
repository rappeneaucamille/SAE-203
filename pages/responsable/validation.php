<?php 
require_once '../../includes/db.php';
include '../../includes/header.php';
if ($_SESSION['role'] !== 'Responsable stage') header('Location: ../../index.php');
?>
<div class="container">
    <h2 class="mb-4">Validation des stages</h2>
    <div class="card shadow-sm p-4">
        <h5>Demandes en attente de signature</h5>
        <table class="table table-hover mt-3">
            <thead class="table-light">
                <tr><th>Étudiant</th><th>Entreprise</th><th>Missions</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Jean DUPONT</td>
                    <td>WebCorp</td>
                    <td>Développement d'une application PHP...</td>
                    <td>
                        <button class="btn btn-success btn-sm btn-confirm" 
                                data-confirm="Voulez-vous vraiment VALIDER ce stage ?">
                            Valider
                        </button>

                        <button class="btn btn-danger btn-sm btn-confirm" 
                                data-confirm="Attention, voulez-vous vraiment REFUSER ce stage ?">
                            Refuser
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>