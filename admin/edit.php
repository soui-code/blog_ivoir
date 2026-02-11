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

// Récupérer l'article
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header("Location: index.php");
    exit;
}

// Traitement modification
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $cat_id  = (int)($_POST['category'] ?? 0);

    if ($title && $content && $cat_id > 0) {
        $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, category_id = ? WHERE id = ?");
        $stmt->execute([$title, $content, $cat_id, $id]);
        header("Location: index.php");
        exit;
    } else {
        $error = "Tous les champs sont obligatoires !";
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

    <form method="POST">
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

      <div class="form-actions">
        <button type="submit" class="btn primary">Enregistrer les modifications</button>
        <a href="index.php" class="btn secondary">Annuler</a>
      </div>
    </form>
  </div>

</body>
</html>