<?php
require_once 'db.php';

// Hantera formulär
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Uppdatera use_translation och use_variants
    $allIds = $pdo->query("SELECT id FROM companies")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($allIds as $id) {
        $use_translation = isset($_POST['use_translation'][$id]) ? 1 : 0;
        $use_variants = isset($_POST['use_variants'][$id]) ? 1 : 0;

        $stmt = $pdo->prepare("UPDATE companies SET use_translation = ?, use_variants = ? WHERE id = ?");
        $stmt->execute([$use_translation, $use_variants, $id]);
    }

    echo "<p style='color: green;'>Inställningar uppdaterade!</p>";
}

// Hämta företag
$companies = $pdo->query("SELECT * FROM companies")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin – Företag</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Företag</h1>

    <form method="POST">
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Företagsnamn</th>
                <th>Telefon</th>
                <th>Postnummer</th>
                <th>Ort</th>
                <th>Översättning</th>
                <th>Tillåt varianter</th>
            </tr>
            <?php foreach ($companies as $company): ?>
                <tr>
                    <td><?= htmlspecialchars($company['id']) ?></td>
                    <td><?= htmlspecialchars($company['name']) ?></td>
                    <td><?= htmlspecialchars($company['phone']) ?></td>
                    <td><?= htmlspecialchars($company['zipcode']) ?></td>
                    <td><?= htmlspecialchars($company['city']) ?></td>
                    <td>
                        <input type="checkbox" name="use_translation[<?= $company['id'] ?>]" <?= $company['use_translation'] ? 'checked' : '' ?>>
                    </td>
                    <td>
                        <input type="checkbox" name="use_variants[<?= $company['id'] ?>]" <?= $company['use_variants'] ? 'checked' : '' ?>>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <button type="submit">Spara ändringar</button>
    </form>
</body>
</html>
