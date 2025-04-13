<?php
session_start();
require 'db.php';

if (!isset($_SESSION['company_id'])) {
    header("Location: login_company.php");
    exit;
}

$company_id = $_SESSION['company_id'];
$success = $error = "";

// Hämta lunch från databasen
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM lunches WHERE id = ? AND company_id = ?");
    $stmt->execute([$_GET['id'], $company_id]);
    $lunch = $stmt->fetch();

    if (!$lunch) {
        $error = "❌ Den här lunchen finns inte eller tillhör inte ditt företag.";
    }
}

// Uppdatera lunchen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $dish_name = $_POST['dish_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    if (!$error) {
        $stmt = $pdo->prepare("UPDATE lunches SET date = ?, dish_name = ?, description = ?, price = ?, category_id = ? WHERE id = ? AND company_id = ?");
        $stmt->execute([$date, $dish_name, $description, $price, $category_id, $_POST['id'], $company_id]);

        $success = "✅ Lunchen har uppdaterats!";
    }
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Ändra lunch</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h2>✏️ Ändra lunch</h2>

    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= $lunch['id'] ?>">
        <input type="date" name="date" value="<?= $lunch['date'] ?>" required>
        <input type="text" name="dish_name" value="<?= htmlspecialchars($lunch['dish_name']) ?>" placeholder="Rättens namn" required>
        <textarea name="description" placeholder="Beskrivning"><?= htmlspecialchars($lunch['description']) ?></textarea>
        <input type="number" name="price" value="<?= $lunch['price'] ?>" step="0.01" placeholder="Pris (kr)" required>
        <select name="category_id">
            <option value="">Välj kategori</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $lunch['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">💾 Spara ändringar</button>
    </form>

    <p><a href="dashboard.php">⬅️ Tillbaka till företagspanelen</a></p>
</div>
</body>
</html>
