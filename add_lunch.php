<?php
require_once 'db.php';
session_start();

$company_id = $_SESSION['company_id'] ?? 1; // Ersätt med riktig inloggning

// Kontrollera om företaget får använda varianter
$stmt = $pdo->prepare("SELECT use_variants FROM companies WHERE id = ?");
$stmt->execute([$company_id]);
$use_variants = $stmt->fetchColumn();

// Hämta variantgrupper och tillhörande alternativ
$variant_groups = $pdo->query("SELECT * FROM variant_groups")->fetchAll(PDO::FETCH_ASSOC);
$variant_options = [];
foreach ($variant_groups as $group) {
    $stmt = $pdo->prepare("SELECT * FROM variant_options WHERE group_id = ?");
    $stmt->execute([$group['id']]);
    $variant_options[$group['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <h1>Lägg till lunchrätt</h1>

    <form action="save_lunch.php" method="POST">
        <label>Rättens namn:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Beskrivning:</label><br>
        <textarea name="description" rows="6" style="width: 100%;"></textarea><br><br>

        <label>Pris (standard):</label><br>
        <input type="number" name="price" step="0.01" required><br><br>

        <?php if ($use_variants): ?>
            <label>Välj variantgrupp (valfritt):</label><br>
            <select id="variant_group" name="variant_group">
                <option value="">– Välj variantgrupp –</option>
                <?php foreach ($variant_groups as $group): ?>
                    <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <div id="variant_preview"></div>

            <script>
                const variantData = <?= json_encode($variant_options) ?>;

                document.getElementById('variant_group').addEventListener('change', function () {
                    const groupId = this.value;
                    const previewDiv = document.getElementById('variant_preview');
                    previewDiv.innerHTML = '';

                    if (variantData[groupId]) {
                        const list = document.createElement('ul');
                        variantData[groupId].forEach(opt => {
                            const item = document.createElement('li');
                            item.textContent = opt.label + ' – ' + parseFloat(opt.price).toFixed(2) + ' kr';
                            list.appendChild(item);
                        });
                        previewDiv.appendChild(list);
                    }
                });
            </script>
        <?php endif; ?>

        <br>
        <button type="submit">Spara lunch</button>
    </form>
</body>
</html>
