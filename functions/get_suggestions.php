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
    if (!is_array($vec1) || !is_array($vec2))
        return 0;

    $dotProduct = 0;
    $normA = 0;
    $normB = 0;

    foreach ($vec1 as $i => $value) {
        $v1 = $vec1[$i] ?? 0;
        $v2 = $vec2[$i] ?? 0;

        $dotProduct += $v1 * $v2;
        $normA += $v1 ** 2;
        $normB += $v2 ** 2;
    }

    $denominator = sqrt($normA) * sqrt($normB);

    if ($denominator == 0)
        return 0;

    return $dotProduct / $denominator;
}

// 1. Get Vector for User Input

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