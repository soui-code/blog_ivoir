<?php
include '../config.php';
include '../PHP/add_admin.php';
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

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div>
        <label for="title">Titre de l'article</label>
        <input type="text" id="title" name="title" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
      </div>

      <div>
        <label for="content">Contenu</label>
        <textarea id="content" name="content" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
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

      <div>
        <label for="image">Image de couverture (jpg, jpeg, png, gif, webp – max 2 Mo)</label>
        <input type="file" id="image" name="image" accept="image/*">
        <small style="display:block; margin-top:0.5rem; color:#777;">Optionnel, mais recommandé pour un beau visuel !</small>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn primary">Publier l'article</button>
        <a href="index.php" class="btn secondary">Annuler</a>
      </div>
    </form>
  </div>
</body>
</html>