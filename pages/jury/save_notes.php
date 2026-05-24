<?php
require_once '../../includes/db.php';
session_start();

if ($_SESSION['role'] !== 'Jury de soutenance' && $_SESSION['role'] !== 'Administrateur') {
    exit('Accès refusé');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_soutenance'];
    $rapport = $_POST['note_rapport'];
    $soutenance = $_POST['note_soutenance'];

    $sql = "UPDATE Soutenance SET note_rapport = ?, note_soutenance = ? WHERE id_soutenance = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$rapport, $soutenance, $id])) {
        header('Location: notes.php?status=success');
    } else {
        header('Location: notes.php?status=error');
    }
}