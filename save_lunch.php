<?php
require_once 'db.php';
session_start();

$company_id = $_SESSION['company_id'] ?? 1; // Ersätt med riktig autentisering

// Säkerställ att nödvändiga fält finns
if (empty($_POST['name']) || empty($_POST['price'])) {
    die("Namn och pris krävs.");
}

$name = $_POST['name'];
$description = $_POST['description'] ?? '';
$price = $_POST['price'];
$variant_group_id = $_POST['variant_group'] ?? null;

// 1. Spara lunchrätten
$stmt = $pdo->prepare("INSERT INTO lunches (company_id, name, description, price) VALUES (?, ?, ?, ?)");
$stmt->execute([$company_id, $name, $description, $price]);
$lunch_id = $pdo->lastInsertId();

// 2. Spara variantalternativ om en grupp har valts
if (!empty($variant_group_id)) {
    $stmt = $pdo->prepare("SELECT id FROM variant_options WHERE group_id = ?");
    $stmt->execute([$variant_group_id]);
    $variant_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($variant_ids as $variant_id) {
        $stmt = $pdo->prepare("INSERT INTO lunch_variants (lunch_id, variant_option_id) VALUES (?, ?)");
        $stmt->execute([$lunch_id, $variant_id]);
    }
}

// 3. Klart – tillbaka till dashboard eller annan vy
header("Location: dashboard.php?success=1");
exit;
