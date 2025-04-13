<?php
session_start();
require 'db.php';
if (!isset($_SESSION['company_id'])) {
    header("Location: login_company.php");
    exit;
}

$company_id = $_SESSION['company_id'];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $template_id = $_POST['template_id'];
    $date = $_POST['date'];

    $stmt = $pdo->prepare("INSERT INTO lunches (company_id, date, dish_name, description, price, category_id, image)
        SELECT company_id, ?, dish_name, description, price, category_id, image FROM lunch_templates WHERE id = ? AND company_id = ?");
    $stmt->execute([$date, $template_id, $company_id]);
    $success = "✅ Rätten har planerats!";
}

$templates = $pdo->prepare("SELECT t.*, c.name AS category FROM lunch_templates t
LEFT JOIN categories c ON t.category_id = c.id
WHERE t.company_id = ? ORDER BY t.created_at DESC");
$templates->execute([$company_id]);
$data = $templates->fetchAll();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Sparade lunchmallar</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
<h2>📂 Sparade lunchmallar</h2>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<?php if (empty($data)): ?>
<p>Inga mallar sparade ännu.</p>
<?php else: ?>
<table>
<tr>
  <th>Rätt</th><th>Beskrivning</th><th>Pris</th><th>Kategori</th><th>Bild</th><th>Planera</th>
</tr>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['dish_name']) ?></td>
  <td><?= htmlspecialchars($row['description']) ?></td>
  <td><?= number_format($row['price'], 2) ?> kr</td>
  <td><?= htmlspecialchars($row['category'] ?? '-') ?></td>
  <td>
    <?php if ($row['image']): ?>
      <?php if (strpos($row['image'], 'http') === 0): ?>
        <img src="<?= $row['image'] ?>" width="100">
      <?php else: ?>
        <img src="uploads/<?= $row['image'] ?>" width="100">
      <?php endif; ?>
    <?php else: ?>-<?php endif; ?>
  </td>
  <td>
    <form method="post">
      <input type="hidden" name="template_id" value="<?= $row['id'] ?>">
      <input type="date" name="date" required>
      <button type="submit">📅 Planera</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<p><a href="dashboard.php">⬅️ Tillbaka till företagspanelen</a></p>
</div>
</body>
</html>
