<?php 
include '../config.php'; 
session_start(); 

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

// Récupérer l'article actuel
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $cat_id  = (int)($_POST['category'] ?? 0);
    $image_path = $article['image']; // Garder l’ancienne par défaut

    // Gestion upload nouvelle image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';

        // Créer dossier si absent
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                $error = "Impossible de créer le dossier uploads.";
            }
        }

        if (empty($error)) {
            $original_name = basename($_FILES['image']['name']);
            $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            $safe_name = time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '_', $original_name);
            $target = $upload_dir . $safe_name;

            if (!in_array($ext, $allowed)) {
                $error = "Format non autorisé (jpg, jpeg, png, gif, webp seulement).";
            } elseif ($_FILES['image']['size'] > 2000000) {
                $error = "Image trop lourde (max 2 Mo).";
            } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                // Supprimer l’ancienne image si elle existe
                if ($image_path && file_exists('../' . $image_path)) {
                    @unlink('../' . $image_path);
                }
                $image_path = 'uploads/' . $safe_name;
            } else {
                $error = "Échec du déplacement de l'image (vérifie permissions dossier uploads).";
            }
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $error = "Erreur upload image (code : " . $_FILES['image']['error'] . ").";
    }

    // Validation et mise à jour
    if (empty($error) && $title && $content && $cat_id > 0) {
        $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, category_id = ?, image = ? WHERE id = ?");
        $stmt->execute([$title, $content, $cat_id, $image_path, $id]);
        $success = "Article modifié avec succès ! 🎉";
    } elseif (empty($error)) {
        $error = "Tous les champs obligatoires doivent être remplis !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier Article - Blog Youssouf</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../CSS/edit_style.css">
</head>
<body>

  <div class="container">
    <h1>Modifier l'Article ✏️</h1>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div>
        <label for="title">Titre de l'article</label>
        <input type="text" id="title" name="title" required value="<?= htmlspecialchars($article['title']) ?>">
      </div>

      <div>
        <label for="content">Contenu</label>
        <textarea id="content" name="content" required><?= htmlspecialchars($article['content']) ?></textarea>
      </div>

      <div>
        <label for="category">Catégorie</label>
        <select id="category" name="category" required>
          <?php 
          $cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
          foreach ($cats as $c) {
            $selected = ($c['id'] == $article['category_id']) ? 'selected' : '';
            echo "<option value='{$c['id']}' $selected>" . htmlspecialchars($c['name']) . "</option>";
          }
          ?>
        </select>
      </div>

      <div>
        <label for="image">Nouvelle image de couverture (optionnel)</label>
        <input type="file" id="image" name="image" accept="image/*">
        <small style="display:block; margin-top:0.5rem; color:#777;">
          Laisser vide pour conserver l’image actuelle
        </small>

        <?php if (!empty($article['image'])): ?>
          <div style="margin-top:1rem;">
            <strong>Image actuelle :</strong><br>
            <img src="../<?= htmlspecialchars($article['image']) ?>" alt="Image actuelle" class="current-image">
          </div>
        <?php endif; ?>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn primary">Enregistrer les modifications</button>
        <a href="index.php" class="btn secondary">Annuler</a>
      </div>
    </form>
  </div>

</body>
</html>