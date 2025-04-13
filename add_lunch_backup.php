<?php
session_start();
require 'db.php';
if (!isset($_SESSION['company_id'])) {
    header("Location: login_company.php");
    exit;
}

$company_id = $_SESSION['company_id'];
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
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
        $stmt = $pdo->prepare("INSERT INTO lunches (company_id, date, dish_name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$company_id, $date, $dish_name, $description, $price, $category_id, $image]);
        $success = "✅ Lunchrätten har lagts till!";
    }
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Lägg till lunch</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
<h2>🍽️ Lägg till lunchrätt</h2>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<form method="post" enctype="multipart/form-data">
  <input type="date" name="date" required>
  <input type="text" name="dish_name" placeholder="Rättens namn" required>
  <textarea name="description" placeholder="Beskrivning" rows="3"></textarea>
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
  <button type="submit">Spara lunch</button>
</form>
<p><a href="dashboard.php">⬅️ Tillbaka till företagspanelen</a></p>
</div>
</body>
</html>
