<?php
session_start();
require 'db.php';
if (!isset($_SESSION['company_id'])) {
    header("Location: login_company.php");
    exit;
}

$company_id = $_SESSION['company_id'];
$stmt = $pdo->prepare("SELECT translate_from FROM companies WHERE id = ?");
$stmt->execute([$company_id]);
$translate_from = $stmt->fetchColumn();

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $dish_name = $_POST['dish_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'] ?: null;
    $image_url = $_POST['image_url'];
    $save_as_template = isset($_POST['save_as_template']);
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

        if ($save_as_template) {
            $stmt2 = $pdo->prepare("INSERT INTO lunch_templates (company_id, dish_name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt2->execute([$company_id, $dish_name, $description, $price, $category_id, $image]);
        }

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
  <style>
    textarea {
      width: 100%;
      min-height: 6em;
      resize: vertical;
      font-family: inherit;
      font-size: 16px;
      padding: 10px;
    }
  </style>
</head>
<body>
<div class="container">
<h2>🍽️ Lägg till lunchrätt</h2>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<form method="post" enctype="multipart/form-data">
  <select name="category_id">
    <option value="">Välj kategori</option>
    <?php foreach ($categories as $cat): ?>
      <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <input type="date" name="date" required>
  <input type="text" name="dish_name" placeholder="Rättens namn" required>

  <?php if (!empty($translate_from)): ?>
    <p>
      <?php if ($translate_from === 'fa') echo '🇮🇷 Översätt från persiska'; ?>
      <?php if ($translate_from === 'th') echo '🇹🇭 Översätt från thailändska'; ?>
    </p>
    <textarea name="description_source" placeholder="Skriv här – översätts automatiskt" oninput="translateLang(this.value)"></textarea>
    <div id="translation-status" style="margin-bottom:5px;color:#007bff;"></div>
  <?php endif; ?>

  <textarea name="description" id="description" placeholder="Beskrivning på svenska"></textarea>
  <input type="number" name="price" step="0.01" placeholder="Pris (kr)" required>
  <label>Ladda upp bild (max 1 MB):</label>
  <input type="file" name="image_file">
  <label>...eller klistra in bild-URL:</label>
  <input type="url" name="image_url" placeholder="https://exempel.se/bild.jpg">
  <label><input type="checkbox" name="save_as_template"> Spara även som mall</label>
  <button type="submit">Spara lunch</button>
</form>
<p><a href="dashboard.php">⬅️ Tillbaka till företagspanelen</a></p>
</div>

<?php if (!empty($translate_from)): ?>
<script>
function translateLang(text) {
  const lang = "<?= $translate_from ?>";
  if (!text.trim()) return;
  document.getElementById("translation-status").textContent = "🔄 Översätter...";
  fetch("translate_api.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ text: text, from: lang, to: "sv" })
  })
  .then(res => res.json())
  .then(data => {
    console.log("Svar från API:", data);
    if (data.translated) {
      document.getElementById("description").value = data.translated;
      document.getElementById("translation-status").textContent = "✅ Översatt";
    } else {
      document.getElementById("translation-status").textContent = "❌ Inget svar från API";
    }
  });
}
</script>
<?php endif; ?>
</body>
</html>
