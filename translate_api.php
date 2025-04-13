<?php
require_once 'config.php'; // innehåller $googleApiKey

$data = json_decode(file_get_contents("php://input"), true);
$text = $data['text'] ?? '';
$from = $data['from'] ?? 'fa';
$to = $data['to'] ?? 'sv';

if (!$text) {
    echo json_encode(['error' => 'Ingen text angiven']);
    exit;
}

$url = 'https://translation.googleapis.com/language/translate/v2?key=' . urlencode($googleApiKey);

$payload = json_encode([
    'q' => $text,
    'source' => $from,
    'target' => $to,
    'format' => 'text'
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_POST, true);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['data']['translations'][0]['translatedText'])) {
    echo json_encode(['translated' => $result['data']['translations'][0]['translatedText']]);
} else {
    echo json_encode(['error' => 'Ingen översättning kunde hämtas', 'raw' => $result]);
}
?>