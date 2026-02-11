<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blog de Youssouf - Tech & Vie en CI 🚀</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

  <div class="container">
    <header>
      <h1>Blog de Youssouf Ouattara</h1>
      <p>Tech, code, vie en Côte d'Ivoire, culture et un max de fun 😄</p>
      <a href="admin/" class="btn">Espace Admin (pour moi)</a>
    </header>

    <div class="articles-grid">
      <?php
      $stmt = $pdo->query("SELECT a.*, c.name as category FROM articles a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.created_at DESC LIMIT 9");
      $hasArticles = false;

      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $hasArticles = true;
        ?>
        <div class="article-card">
          <?php if (!empty($row['image'])): ?>
            <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
          <?php else: ?>
            <div class="placeholder-img"></div>
          <?php endif; ?>

          <span class="category"><?= htmlspecialchars($row['category'] ?? 'Général') ?></span>

          <h3>
            <a href="article.php?id=<?= $row['id'] ?>">
              <?= htmlspecialchars($row['title']) ?>
            </a>
          </h3>

          <p><?= substr(strip_tags($row['content']), 0, 120) ?>...</p>

          <small><?= date('d M Y', strtotime($row['created_at'])) ?></small>
        </div>
        <?php
      }

      if (!$hasArticles):
      ?>
        <div class="no-articles">
          Pas encore d'articles... <br>
          Connecte-toi en admin et publie le premier ! 🚀
        </div>
      <?php endif; ?>
    </div>

    <footer>
      © <?= date('Y') ?> Youssouf Ouattara | Blog perso – Code with passion & attiéké ☕
    </footer>
  </div>

</body>
</html>