<?php
require 'db.php';
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postal_code = $_POST['postal_code'];
    $region = $_POST['region'];
    $phone = $_POST['phone'];

    $stmt = $pdo->prepare("INSERT INTO companies (name, email, password, address, city, postal_code, region, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    try {
        $stmt->execute([$name, $email, $password, $address, $city, $postal_code, $region, $phone]);
        $success = "✅ Företaget har registrerats!";
    } catch (PDOException $e) {
        $error = "❌ Det gick inte att registrera företaget. E-posten kan vara upptagen.";
    }
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Registrera företag</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
<h2>🏢 Registrera ditt företag</h2>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<form method="post">
  <input type="text" name="name" placeholder="Företagsnamn" required>
  <input type="email" name="email" placeholder="E-post" required>
  <input type="password" name="password" placeholder="Lösenord" required>
  <input type="text" name="address" placeholder="Adress" required>
  <input type="text" name="city" placeholder="Ort" required>
  <input type="text" name="postal_code" placeholder="Postnummer" required>
  <input type="text" name="region" placeholder="Län" required>
  <input type="text" name="phone" placeholder="Telefonnummer" required>
  <button type="submit">Registrera</button>
</form>
</div>
</body>
</html>
