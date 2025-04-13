<?php
session_start();
require 'db.php';

if (!isset($_SESSION['company_id'])) {
    header("Location: login_company.php");
    exit;
}

$company_id = $_SESSION['company_id'];

// Ta bort lunch
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM lunches WHERE id = ? AND company_id = ?");
    $stmt->execute([$_GET['id'], $company_id]);
    header("Location: dashboard.php");
    exit;
}

header("Location: dashboard.php");
