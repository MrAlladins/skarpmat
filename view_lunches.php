<?php
session_start();
require 'db.php';
if (!isset($_SESSION['company_id'])) {
    header("Location: login_company.php");
    exit;
}

$company_id = $_SESSION['company_id'];

$stmt = $pdo->prepare("SELECT l.*, c.name AS category FROM lunches l
LEFT JOIN categories c ON l.category_id = c.id
WHERE l.company_id = ? ORDER BY l.date DESC");
$stmt->execute([$company_id]);
$lunches = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Mina lunchrätter</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
<h2>📋 Mina lunchrätter</h2>
<?php if (empty($lunches)): ?>
  <p>Inga lunchrätter tillagda ännu.</p>
<?php else: ?>
  <table>
    <tr>
      <th>Datum</th>
      <th>Rätt</th>
      <th>Beskrivning</th>
      <th>Pris</th>
      <th>Kategori</th>
      <th>Bild</th>
    </tr>
    <?php foreach ($lunches as $l): ?>
      <tr>
        <td><?= htmlspecialchars($l['date']) ?></td>
        <td><?= htmlspecialchars($l['dish_name']) ?></td>
        <td><?= nl2br(htmlspecialchars($l['description'])) ?></td>
        <td><?= number_format($l['price'], 2) ?> kr</td>
        <td><?= htmlspecialchars($l['category'] ?? '-') ?></td>
        <td>
          <?php if ($l['image']): ?>
            <?php if (strpos($l['image'], 'http') === 0): ?>
              <img src="<?= $l['image'] ?>" alt="Bild" width="100">
            <?php else: ?>
              <img src="uploads/<?= $l['image'] ?>" alt="Bild" width="100">
            <?php endif; ?>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>
<p><a href="dashboard.php">⬅️ Tillbaka till panelen</a></p>
</div>
</body>
</html>
