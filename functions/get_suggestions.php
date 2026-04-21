<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../init.php";

$apiKey = $_ENV['GOOGLE_API_KEY'];
$input = $_GET['text'] ?? '';

if (empty($input)) {
    echo json_encode(['error' => 'No text provided']);
    exit;
}

/**
 * Math: Cosine Similarity
 * Compares two arrays of numbers to see how "aligned" they are.
 */
function cosineSimilarity($vec1, $vec2)
{
    $dotProduct = 0;
    $normA = 0;
    $normB = 0;
    foreach ($vec1 as $i => $value) {
        $dotProduct += $vec1[$i] * $vec2[$i];
        $normA += $vec1[$i] ** 2;
        $normB += $vec2[$i] ** 2;
    }
    return $dotProduct / (sqrt($normA) * sqrt($normB));
}

// 1. Get Vector for User Input
function getEmbedding($text, $key)
{
    $modelPath = "models/gemini-embedding-001";
    $url = "https://generativelanguage.googleapis.com/v1beta/{$modelPath}:embedContent?key=" . $key;
    $payload = json_encode(["model" => $modelPath, "content" => ["parts" => [["text" => $text]]]]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $res = json_decode(curl_exec($ch), true);
    return $res['embedding']['values'] ?? null;
}

$userVector = getEmbedding($input, $apiKey);
if (!$userVector) {
    echo json_encode(['error' => 'API Failure']);
    exit;
}

// 2. Find Best Matches
$results = [];
$tables = [
    'category' => ['cat_id', 'cat_vector'],
    'module' => ['mod_id', 'mod_vector'],
    'severity' => ['sev_id', 'sev_vector']
];

$db = new PDO("mysql:host=$_ENV[DB_HOST];dbname=$_ENV[DB_DATABASE]", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);

foreach ($tables as $table => $cols) {
    $stmt = $db->query("SELECT {$cols[0]}, {$cols[1]} FROM $table WHERE {$cols[1]} IS NOT NULL");
    $bestMatch = null;
    $highestScore = -1;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dbVector = json_decode($row[$cols[1]], true);
        $score = cosineSimilarity($userVector, $dbVector);

        if ($score > $highestScore) {
            $highestScore = $score;
            $bestMatch = $row[$cols[0]];
        }
    }
    $results[$table] = $bestMatch;
}

echo json_encode($results);