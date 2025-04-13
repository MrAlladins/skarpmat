<?php
session_start();
require 'db.php';

if (!isset($_SESSION['company_id'])) {
    header("Location: login_company.php");
    exit;
}

$company_id = $_SESSION['company_id'];
$stmt = $pdo->prepare("SELECT name FROM companies WHERE id = ?");
$stmt->execute([$company_id]);
$company_name = $stmt->fetchColumn();

$lunch_stmt = $pdo->prepare("SELECT * FROM lunches WHERE company_id = ? AND date >= CURDATE() ORDER BY date ASC");
$lunch_stmt->execute([$company_id]);
$lunches = $lunch_stmt->fetchAll();

function getWeekLabel($date) {
    $ts = strtotime($date);
    $week = date("W", $ts);
    $year = date("o", $ts);
    return "Vecka $week, $year";
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Företagspanel</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .container { max-width: 700px; margin: 40px auto; font-family: sans-serif; }
    h2 { margin-bottom: 20px; }
    .buttons a {
      display: block;
      padding: 12px;
      background: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      margin-bottom: 10px;
      text-align: center;
    }
    .buttons a:hover {
      background: #0056b3;
    }
    .lunch-item {
      background: #f8f8f8;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      border-left: 5px solid #007bff;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>👋 Välkommen, <?= htmlspecialchars($company_name) ?>!</h2>

  <div class="buttons">
    <a href="add_lunch.php">➕ Lägg till lunch</a>
    <a href="my_templates.php">📋 Hantera lunchmallar</a>
    <a href="add_template.php">➕ Lägg till mall</a>
    <a href="logout_company.php" style="background:#dc3545;">🚪 Logga ut</a>
  </div>

  <h3>📅 Kommande luncher</h3>

  <?php foreach ($lunches as $lunch): ?>
    <?php
      $formatter = new IntlDateFormatter('sv_SE', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
      $formatter->setPattern('EEEE d MMMM yyyy');
      $formattedDate = ucfirst($formatter->format(new DateTime($lunch['date'])));
    ?>
    <div class="lunch-item">
      📅 <?= $formattedDate ?><br>
      <strong><?= htmlspecialchars($lunch['dish_name']) ?></strong><br>
      <?= nl2br(htmlspecialchars($lunch['description'])) ?><br>
      <a href="edit_lunch.php?id=<?= $lunch['id'] ?>">✏️ Ändra</a> |
      <a href="delete_lunch.php?id=<?= $lunch['id'] ?>" onclick="return confirm('Är du säker på att du vill ta bort denna lunch?');">🗑️ Radera</a>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>
