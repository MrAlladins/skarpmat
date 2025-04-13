<?php
require_once 'db.php';

// Ny grupp
if (isset($_POST['new_group_name']) && !empty($_POST['new_group_name'])) {
    $stmt = $pdo->prepare("INSERT INTO variant_groups (name) VALUES (?)");
    $stmt->execute([$_POST['new_group_name']]);
}

// Nytt alternativ
if (isset($_POST['group_id'], $_POST['label'], $_POST['price'])) {
    $stmt = $pdo->prepare("INSERT INTO variant_options (group_id, label, price) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['group_id'], $_POST['label'], $_POST['price']]);
}

// Ta bort alternativ
if (isset($_GET['delete_option'])) {
    $stmt = $pdo->prepare("DELETE FROM variant_options WHERE id = ?");
    $stmt->execute([$_GET['delete_option']]);
}

// Hämta alla grupper och alternativ
$groups = $pdo->query("SELECT * FROM variant_groups")->fetchAll(PDO::FETCH_ASSOC);
$options = [];
foreach ($groups as $group) {
    $stmt = $pdo->prepare("SELECT * FROM variant_options WHERE group_id = ?");
    $stmt->execute([$group['id']]);
    $options[$group['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin – Variantgrupper</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Variantgrupper</h1>

    <h2>Skapa ny grupp</h2>
    <form method="POST">
        <input type="text" name="new_group_name" placeholder="Gruppnamn" required>
        <button type="submit">Skapa</button>
    </form>

    <hr>

    <?php foreach ($groups as $group): ?>
        <h3><?= htmlspecialchars($group['name']) ?></h3>
        <form method="POST" style="margin-bottom: 10px;">
            <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
            <input type="text" name="label" placeholder="Ex: Kyckling" required>
            <input type="number" name="price" placeholder="Pris" step="0.01" required>
            <button type="submit">Lägg till alternativ</button>
        </form>

        <?php if (!empty($options[$group['id']])): ?>
            <ul>
                <?php foreach ($options[$group['id']] as $opt): ?>
                    <li>
                        <?= htmlspecialchars($opt['label']) ?> – <?= number_format($opt['price'], 2, ',', ' ') ?> kr
                        <a href="?delete_option=<?= $opt['id'] ?>" onclick="return confirm('Ta bort detta alternativ?')">❌</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p><em>Inga alternativ ännu.</em></p>
        <?php endif; ?>
        <hr>
    <?php endforeach; ?>
</body>
</html>
