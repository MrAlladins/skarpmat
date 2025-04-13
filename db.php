<?php
$host = "localhost";
$dbname = "u885682125_sharpmat";
$username = "u885682125_sharpjonas";
$password = "Jonas366#";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("Databasanslutning misslyckades: " . $e->getMessage());
}
?>