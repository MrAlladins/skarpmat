<?php
session_start();
require 'db.php';

if (!isset($_SESSION['company_id'])) {
    header("Location: login_company.php");
    exit;
}

$company_id = $_SESSION['company_id'];
$success = $error = "";

// Hämta kategorier från databasen
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

// När formuläret skickas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dish_name = $_POST['dish_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'] ?: null;
    $image_url = $_POST['image_url'];
    $image = "";

    if (!empty($_FILES['image_file']['name'])) {
        if ($_FILES['image_file']['size'] <= 1048576) {
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $target = "uploads/" . $filename;
            if (!is_dir("uploads")) mkdir("uploads");
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target)) {
                $image = $filename;
            } else {
                $error = "❌ Bilden kunde inte laddas upp.";
            }
        } else {
            $error = "❌ Bildfilen får max vara 1 MB.";
        }
    } elseif (!empty($image_url)) {
        $image = $image_url;
    }

    if (!$error) {
        // Spara som mall
        $stmt = $pdo->prepare("INSERT INTO lunch_templates (company_id, dish_name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$company_id, $dish_name, $description, $price, $category_id, $image]);

        $success = "✅ Mallen har lagts till!";
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Skapa mall</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
  <h2>🍽️ Skapa mall för lunch</h2>

  <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
  <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

  <form method="post" enctype="multipart/form-data">
    <select name="category_id" required>
        <option value="">Välj kategori</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="dish_name" placeholder="Rättens namn" required>
    <textarea name="description" placeholder="Beskrivning" required></textarea>
    <input type="number" name="price" step="0.01" placeholder="Pris (kr)" required>
    <select name="category_id">
      <option value="">Välj kategori</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <label>Ladda upp bild (max 1 MB):</label>
    <input type="file" name="image_file">
    <label>...eller klistra in bild-URL:</label>
    <input type="url" name="image_url" placeholder="https://exempel.se/bild.jpg">
    <button type="submit">💾 Spara mall</button>
  </form>

  <p><a href="dashboard.php">⬅️ Tillbaka till företagspanelen</a></p>
</div>
</body>
</html>
