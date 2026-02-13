<?php

session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $cat_id  = (int)($_POST['category'] ?? 0);
    $image_path = null;

    // Gestion de l'upload image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';

        // Créer le dossier s'il n'existe pas
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                $error = "Impossible de créer le dossier uploads. Vérifie les permissions du dossier parent.";
            }
        }

        if (empty($error)) {
            $original_name = basename($_FILES['image']['name']);
            $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            // Nettoyage nom fichier + timestamp unique
            $safe_name = time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '_', $original_name);
            $target = $upload_dir . $safe_name;

            if (!in_array($ext, $allowed)) {
                $error = "Format d'image non autorisé (seulement jpg, jpeg, png, gif, webp).";
            } elseif ($_FILES['image']['size'] > 2000000) { // 2 Mo max
                $error = "L'image est trop lourde (maximum 2 Mo).";
            } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_path = 'uploads/' . $safe_name;
            } else {
                $error = "Échec du déplacement de l'image. Vérifie les permissions du dossier uploads (755 ou 777).";
            }
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $error = "Erreur lors de l'envoi de l'image (code erreur : " . $_FILES['image']['error'] . ").";
    }

    // Validation des champs obligatoires
    if (empty($error) && $title && $content && $cat_id > 0) {
        $stmt = $pdo->prepare("INSERT INTO articles (title, content, category_id, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $cat_id, $image_path]);
        $success = "Article publié avec succès ! 🎉";
    } elseif (empty($error)) {
        $error = "Tous les champs obligatoires doivent être remplis !";
    }
}