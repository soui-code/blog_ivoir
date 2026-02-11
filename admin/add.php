<?php
include '../config.php';
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $cat_id  = (int)($_POST['category'] ?? 0);

    if ($title && $content && $cat_id > 0) {
        $stmt = $pdo->prepare("INSERT INTO articles (title, content, category_id) VALUES (?, ?, ?)");
        $stmt->execute([$title, $content, $cat_id]);
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
  <title>Ajouter un Article - Blog Youssouf</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../CSS/add_style.css">
</head>
<body>

  <div class="container">
    <h1>Ajouter un Nouvel Article 🚀</h1>

    <?php if (isset($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div>
        <label for="title">Titre de l'article</label>
        <input type="text" id="title" name="title" placeholder="Ex: Les 5 meilleurs frameworks PHP en 2026" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
      </div>

      <div>
        <label for="content">Contenu</label>
        <textarea id="content" name="content" placeholder="Écris ton article ici... (texte brut pour l'instant)" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
      </div>

      <div>
        <label for="category">Catégorie</label>
        <select id="category" name="category" required>
          <option value="">-- Choisir une catégorie --</option>
          <?php
          $cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
          foreach ($cats as $c) {
            $selected = (isset($_POST['category']) && $_POST['category'] == $c['id']) ? 'selected' : '';
            echo "<option value='{$c['id']}' $selected>" . htmlspecialchars($c['name']) . "</option>";
          }
          ?>
        </select>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn primary">Publier l'article</button>
        <a href="index.php" class="btn secondary">Annuler</a>
      </div>
    </form>
  </div>

</body>
</html>