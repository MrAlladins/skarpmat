<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE email = ?");
    $stmt->execute([$email]);
    $company = $stmt->fetch();

    if ($company && password_verify($password, $company['password'])) {
        $_SESSION['company_id'] = $company['id'];
        $_SESSION['company_name'] = $company['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "❌ Fel e-post eller lösenord.";
    }
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Företagsinloggning</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
<h2>🔐 Företagsinloggning</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
  <input type="email" name="email" placeholder="E-post" required>
  <input type="password" name="password" placeholder="Lösenord" required>
  <button type="submit">Logga in</button>
</form>
<p><a href="register_company.php">Registrera företag</a></p>
</div>
</body>
</html>
