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
  <title><?= htmlspecialchars($article['title']) ?> - Blog Youssouf</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container single">
    <a href="index.php" class="back">&larr; Retour au blog</a>
    <h1><?= htmlspecialchars($article['title']) ?></h1>
    <span class="category"><?= htmlspecialchars($article['category'] ?? 'Général') ?></span>
    <small><?= date('d M Y à H:i', strtotime($article['created_at'])) ?></small>
    <?php if ($article['image']): ?>
      <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="full-img">
    <?php endif; ?>
    <div class="content"><?= nl2br(htmlspecialchars($article['content'])) ?></div>
  </div>
</body>
</html>