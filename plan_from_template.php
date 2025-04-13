<?php
session_start();
require 'db.php';

if (!isset($_SESSION['company_id'])) {
    header("Location: login_company.php");
    exit;
}

$company_id = $_SESSION['company_id'];
$success = "";

// Om formulär skickats in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_templates'], $_POST['date'])) {
    $selected = $_POST['selected_templates'];
    $date = $_POST['date'];

    $stmt = $pdo->prepare("SELECT * FROM lunch_templates WHERE id = ? AND company_id = ?");
    $insert = $pdo->prepare("INSERT INTO lunches (company_id, date, dish_name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?, ?, ?)");

    foreach ($selected as $template_id) {
        $stmt->execute([$template_id, $company_id]);
        $tpl = $stmt->fetch();
        if ($tpl) {
            $insert->execute([
                $company_id,
                $date,
                $tpl['dish_name'],
                $tpl['description'],
                $tpl['price'],
                $tpl['category_id'],
                $tpl['image']
            ]);
        }
    }

    $success = "✅ Luncher har lagts till för $date.";
}

// Hämta mallar
$stmt = $pdo->prepare("SELECT * FROM lunch_templates WHERE company_id = ?");
$stmt->execute([$company_id]);
$templates = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Planera lunch från mall</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container { max-width: 700px; margin: 40px auto; }
        .template-box { border: 1px solid #ccc; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .template-box label { font-weight: bold; display: block; }
    </style>
</head>
<body>
<div class="container">
    <h2>📆 Planera lunch från mall</h2>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <form method="post">
        <label>Välj datum för luncherna:</label>
        <input type="date" name="date" required>
        <br><br>
        <?php foreach ($templates as $tpl): ?>
            <div class="template-box">
                <label><input type="checkbox" name="selected_templates[]" value="<?= $tpl['id'] ?>"> <?= htmlspecialchars($tpl['dish_name']) ?></label>
                <div><?= nl2br(htmlspecialchars($tpl['description'])) ?></div>
            </div>
        <?php endforeach; ?>
        <button type="submit">📌 Planera valda luncher</button>
    </form>
    <p><a href="dashboard.php">⬅️ Tillbaka till företagspanelen</a></p>
</div>
</body>
</html>
