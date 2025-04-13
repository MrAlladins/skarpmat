<?php
require_once 'db.php';

$lunch_id = $_GET['id'] ?? null;
if (!$lunch_id) {
    die("Ingen lunchrätt angiven.");
}

// Hämta lunchrätten
$stmt = $pdo->prepare("SELECT * FROM lunches WHERE id = ?");
$stmt->execute([$lunch_id]);
$lunch = $stmt->fetch();
if (!$lunch) {
    die("Lunchrätt hittades inte.");
}

// Hämta alla variantgrupper och alternativ
$groups = $pdo->query("SELECT * FROM variant_groups")->fetchAll(PDO::FETCH_ASSOC);
$group_options = [];
foreach ($groups as $group) {
    $stmt = $pdo->prepare("SELECT * FROM variant_options WHERE group_id = ?");
    $stmt->execute([$group['id']]);
    $group_options[$group['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hämta redan valda varianter för denna lunch
$stmt = $pdo->prepare("SELECT variant_option_id FROM lunch_variants WHERE lunch_id = ?");
$stmt->execute([$lunch_id]);
$selected_variants = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'variant_option_id');

// Hantera POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ta bort gamla
    $stmt = $pdo->prepare("DELETE FROM lunch_variants WHERE lunch_id = ?");
    $stmt->execute([$lunch_id]);

    // Lägg till nya
    if (!empty($_POST['variants'])) {
        foreach ($_POST['variants'] as $variant_id) {
            $stmt = $pdo->prepare("INSERT INTO lunch_variants (lunch_id, variant_option_id) VALUES (?, ?)");
            $stmt->execute([$lunch_id, $variant_id]);
        }
    }

    header("Location: dashboard.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Redigera varianter</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Redigera varianter för: <?= htmlspecialchars($lunch['name']) ?></h2>
    <p><em><?= htmlspecialchars($lunch['description']) ?></em></p>

    <form method="POST">
        <?php foreach ($groups as $group): ?>
            <h4><?= htmlspecialchars($group['name']) ?></h4>
            <ul>
                <?php foreach ($group_options[$group['id']] as $option): ?>
                    <li>
                        <label>
                            <input type="checkbox" name="variants[]" value="<?= $option['id'] ?>"
                                <?= in_array($option['id'], $selected_variants) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($option['label']) ?> – <?= number_format($option['price'], 2, ',', ' ') ?> kr
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
        <br>
        <button type="submit">Spara varianter</button>
    </form>

    <p><a href="dashboard.php">← Tillbaka</a></p>
</body>
</html>
