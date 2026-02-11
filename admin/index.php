<?php
include '../config.php';
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    if (isset($_POST['pass']) && $_POST['pass'] === 'tonmotdepasse123') { // CHANGE ÇA par un mot de passe fort !
        $_SESSION['admin'] = true;
    } else {
        // Formulaire de login stylé fun
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Connexion Admin - Blog Youssouf</title>
          <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
          <link rel="stylesheet" type="text/css" href="../CSS/index_style.css">
        </head>
        <body>
          <div class="login-box">
            <h1>Admin Zone 🚀</h1>
            <p style="margin-bottom: 2rem; color: var(--dark);">Entre ton mot de passe secret !</p>
            <form method="POST">
              <input type="password" name="pass" placeholder="Mot de passe admin" required autofocus>
              <button type="submit">Entrer</button>
            </form>
          </div>
        </body>
        </html>
        <?php
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Blog - Youssouf</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary:   #FF6B35;
      --secondary: #00D4FF;
      --accent:    #FFD60A;
      --dark:      #1a1a2e;
      --light:     #f8f9fa;
      --gray:      #6c757d;
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #f0f4ff 0%, #e6faff 100%);
      color: var(--dark);
      min-height: 100vh;
      padding: 2rem 1rem;
    }

    .container {
      max-width: 1100px;
      margin: 0 auto;
      background: white;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 212, 255, 0.15);
      padding: 2.5rem;
      overflow-x: auto;
    }

    h1 {
      font-family: 'Poppins', sans-serif;
      color: var(--primary);
      text-align: center;
      font-size: 2.6rem;
      margin-bottom: 2.5rem;
      position: relative;
    }

    h1::after {
      content: '';
      width: 100px;
      height: 6px;
      background: var(--accent);
      position: absolute;
      bottom: -12px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 3px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      padding: 0.9rem 2rem;
      font-size: 1.05rem;
      font-weight: 600;
      border-radius: 50px;
      text-decoration: none;
      transition: all 0.3s ease;
      font-family: 'Poppins', sans-serif;
    }

    .btn.primary {
      background: var(--primary);
      color: white;
    }

    .btn.primary:hover {
      background: #ff824d;
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(255,107,53,0.3);
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 12px;
      margin-top: 1rem;
    }

    th, td {
      padding: 1.2rem 1.5rem;
      text-align: left;
      background: var(--light);
    }

    th {
      background: var(--secondary);
      color: white;
      font-weight: 600;
      font-family: 'Poppins', sans-serif;
      text-transform: uppercase;
      font-size: 0.95rem;
      letter-spacing: 0.5px;
    }

    tr {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      transition: all 0.3s;
    }

    tr:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 30px rgba(0,212,255,0.15);
    }

    td a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
      margin-right: 1rem;
      transition: color 0.2s;
    }

    td a:hover {
      color: #ff824d;
    }

    td a:last-child {
      color: #e74c3c;
    }

    td a:last-child:hover {
      color: #c0392b;
    }

    @media (max-width: 768px) {
      .header {
        flex-direction: column;
        text-align: center;
      }
      table, thead, tbody, th, td, tr {
        display: block;
      }
      thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
      }
      tr {
        margin-bottom: 1.5rem;
        border: 1px solid #ddd;
      }
      td {
        border: none;
        position: relative;
        padding-left: 50%;
        text-align: right;
      }
      td:before {
        content: attr(data-label);
        position: absolute;
        left: 1rem;
        width: 45%;
        padding-right: 1rem;
        white-space: nowrap;
        font-weight: 600;
        text-align: left;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <h1>Gestion du Blog 🚀</h1>

    <div class="header">
      <div></div> <!-- espace vide pour centrage -->
      <a href="add.php" class="btn primary">+ Ajouter un article</a>
    </div>

    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Catégorie</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $stmt = $pdo->query("SELECT a.*, c.name as cat FROM articles a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.id DESC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          echo "<tr>";
          echo "<td data-label='Titre'>" . htmlspecialchars($row['title']) . "</td>";
          echo "<td data-label='Catégorie'>" . htmlspecialchars($row['cat'] ?? '-') . "</td>";
          echo "<td data-label='Date'>" . date('d/m/Y', strtotime($row['created_at'])) . "</td>";
          echo "<td data-label='Actions'>";
          echo "<a href='edit.php?id={$row['id']}'>Modifier</a> | ";
          echo "<a href='delete.php?id={$row['id']}' onclick='return confirm(\"Vraiment supprimer cet article ?\")'>Supprimer</a>";
          echo "</td>";
          echo "</tr>";
        }
        if ($stmt->rowCount() === 0) {
          echo "<tr><td colspan='4' style='text-align:center; padding:3rem; color:var(--gray);'>Aucun article pour l'instant... Ajoute-en un ! 😄</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

</body>
</html>