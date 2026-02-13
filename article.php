<?php 
include 'config.php'; 
if (!isset($_GET['id'])) header("Location: index.php");
$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT a.*, c.name as category FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$article) header("Location: index.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($article['title']) ?> - Blog Youssouf</title>

  <!-- Bootstrap 5 (pour grille responsive + utilitaires) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Tes polices -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Inter:wght@400;500&display=swap" rel="stylesheet">

 <link rel="stylesheet" href="style-article.css">
</head>
<body>

  <div class="article-container">

    <a href="index.php" class="back">&larr; Retour au blog</a>

    <h1><?= htmlspecialchars($article['title']) ?></h1>

    <div class="meta">
      <span class="category"><?= htmlspecialchars($article['category'] ?? 'Général') ?></span>
      <span><?= date('d M Y à H:i', strtotime($article['created_at'])) ?></span>
    </div>

    <?php if ($article['image']): ?>
      <div class="article-img-wrapper">
        <img 
          src="<?= htmlspecialchars($article['image']) ?>" 
          alt="<?= htmlspecialchars($article['title']) ?>" 
          class="article-img"
          loading="lazy"        
        >
      </div>
    <?php endif; ?>

    <div class="content">
      <?= nl2br(htmlspecialchars($article['content'])) ?>
    </div>

  </div>

  <!-- Bootstrap JS (optionnel ici) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>