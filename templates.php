<?php
session_start();
require 'db.php';

if (!isset($_SESSION['company_id'])) {
    header("Location: login_company.php");
    exit;
}

$company_id = $_SESSION['company_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM lunch_templates WHERE id = ? AND company_id = ?");
        $stmt->execute([$_POST['delete_id'], $company_id]);
    } elseif (isset($_POST['id'])) {
        $stmt = $pdo->prepare("UPDATE lunch_templates SET dish_name = ?, description = ?, price = ?, category_id = ? WHERE id = ? AND company_id = ?");
        $stmt->execute([
            $_POST['dish_name'],
            $_POST['description'],
            $_POST['price'],
            $_POST['category_id'],
            $_POST['id'],
            $company_id
        ]);
    }
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
$stmt = $pdo->prepare("SELECT * FROM lunch_templates WHERE company_id = ?");
$stmt->execute([$company_id]);
$templates = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Lunchmallar</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        form.template { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 8px; }
        input, textarea, select { width: 100%; margin-bottom: 10px; padding: 8px; }
        .actions { display: flex; justify-content: space-between; }
        .actions button { width: 48%; }
    </style>
</head>
<body>
<div class="container">
    <h2>📋 Hantera lunchmallar</h2>
    <?php foreach ($templates as $tpl): ?>
        <form class="template" method="post">
            <input type="hidden" name="id" value="<?= $tpl['id'] ?>">
            <input type="text" name="dish_name" value="<?= htmlspecialchars($tpl['dish_name']) ?>" placeholder="Rättens namn" required>
            <textarea name="description" placeholder="Beskrivning"><?= htmlspecialchars($tpl['description']) ?></textarea>
            <input type="number" name="price" value="<?= $tpl['price'] ?>" step="0.01" placeholder="Pris (kr)" required>
            <select name="category_id">
                <option value="">Välj kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $tpl['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="actions">
                <button type="submit">💾 Spara ändringar</button>
        </form>
        <form method="post" onsubmit="return confirm('Ta bort denna mall?');">
            <input type="hidden" name="delete_id" value="<?= $tpl['id'] ?>">
            <button type="submit" style="background:#d9534f;">🗑️ Ta bort</button>
        </form>
            </div>
    <?php endforeach; ?>
    <p><a href="dashboard.php">⬅️ Tillbaka till företagspanelen</a></p>
</div>
</body>
</html>
