<?php
include '../config.php'; 
session_start(); 
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Optionnel : supprimer l'image si tu ajoutes l'upload plus tard
    // $stmt = $pdo->prepare("SELECT image FROM articles WHERE id = ?");
    // $stmt->execute([$id]);
    // $img = $stmt->fetchColumn();
    // if ($img && file_exists('../' . $img)) unlink('../' . $img);

    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;
?>