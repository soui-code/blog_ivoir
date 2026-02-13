<?php 
include 'config.php'; 

// Récupérer toutes les catégories pour le filtre
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Catégorie sélectionnée (0 = toutes)
$selected_cat = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Construction de la requête
$sql = "SELECT a.*, c.name as category FROM articles a LEFT JOIN categories c ON a.category_id = c.id";
$params = [];

if ($selected_cat > 0) {
    $sql .= " WHERE a.category_id = ?";
    $params[] = $selected_cat;
}

$sql .= " ORDER BY a.created_at DESC LIMIT 9";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
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

    <div class="filter-zone">
      <form method="GET" class="filter-form">
        <select name="category" id="category">
          <option value="0" <?= $selected_cat === 0 ? 'selected' : '' ?>>Toutes les catégories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $selected_cat === $cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit">Filtrer</button>
      </form>
    </div>

    <div class="articles-grid">
      <?php if (empty($articles)): ?>
        <div class="no-articles">
          Aucun article trouvé pour cette catégorie...<br>
          <a href="?category=0" style="color:var(--primary); font-weight:bold;">Voir tous les articles</a>
        </div>
      <?php else: ?>
        <?php foreach ($articles as $row): ?>
          <div class="article-card">
            <div class="article-img-container">
              <?php if (!empty($row['image'])): ?>
                <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
              <?php else: ?>
                <div class="placeholder-img">Pas d'image</div>
              <?php endif; ?>
            </div>

            <!-- Catégorie dans la zone blanche, juste avant le titre -->
            <div class="category-badge">
              <?= htmlspecialchars($row['category'] ?? 'Général') ?>
            </div>

            <h3>
              <a href="article.php?id=<?= $row['id'] ?>">
                <?= htmlspecialchars($row['title']) ?>
              </a>
            </h3>

            <p><?= substr(strip_tags($row['content']), 0, 120) ?>...</p>

            <small><?= date('d M Y', strtotime($row['created_at'])) ?></small>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <footer>
      © <?= date('Y') ?> Youssouf Ouattara | Blog perso – Code with passion & attiéké ☕
    </footer>
  </div>

</body>
</html>