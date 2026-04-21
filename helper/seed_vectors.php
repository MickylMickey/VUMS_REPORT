<?php
set_time_limit(300);
require_once __DIR__ . "/../init.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Configuration
$apiKey = $_ENV['GOOGLE_API_KEY'];
$db = new PDO("mysql:host=$_ENV[DB_HOST];dbname=$_ENV[DB_DATABASE]", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);

/**
 * Fetch embedding from Gemini API
 */
function getGeminiEmbedding($text, $key)
{
    // USE v1beta and gemini-embedding-001
    $modelPath = "models/gemini-embedding-001";
    $url = "https://generativelanguage.googleapis.com/v1beta/{$modelPath}:embedContent?key=" . $key;

    $payload = [
        "model" => $modelPath,
        "content" => ["parts" => [["text" => $text]]]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    // Bypass SSL for local Windows development
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $result = curl_exec($ch);

    if ($result === false) {
        echo "❌ cURL Error: " . curl_error($ch) . "<br>";
        return null;
    }

    $response = json_decode($result, true);

    if (isset($response['error'])) {
        echo "<br>❌ Google API Error: " . $response['error']['message'] . " (Code: " . $response['error']['code'] . ")<br>";
        return null;
    }

    // Access the values array
    return isset($response['embedding']['values']) ? json_encode($response['embedding']['values']) : null;

    // Note: curl_close($ch) is removed because it's deprecated in PHP 8.5+
}

// List of tables to process: [TableName => [ID_Column, Desc_Column, Vector_Column]]
$tables = [
    'category' => ['cat_id', 'cat_desc', 'cat_vector'],
    'module' => ['mod_id', 'mod_desc', 'mod_vector'],
    'severity' => ['sev_id', 'sev_desc', 'sev_vector']
];

foreach ($tables as $tableName => $cols) {
    echo "## Checking $tableName... <br>";

    // CHANGE: Check for both NULL and empty strings
    $stmt = $db->prepare("SELECT {$cols[0]}, {$cols[1]} FROM $tableName WHERE {$cols[2]} IS NULL OR {$cols[2]} = ''");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($rows) . " rows to process.<br>";

    foreach ($rows as $row) {
        $vector = getGeminiEmbedding($row[$cols[1]], $apiKey);

        if ($vector) {
            $update = $db->prepare("UPDATE $tableName SET {$cols[2]} = ? WHERE {$cols[0]} = ?");
            $success = $update->execute([$vector, $row[$cols[0]]]);

            if ($success) {
                echo "✅ Updated ID: " . $row[$cols[0]] . " with " . strlen($vector) . " bytes of data.<br>";
            } else {
                echo "❌ Database Error on ID " . $row[$cols[0]] . ": " . implode(" ", $update->errorInfo()) . "<br>";
            }
        } else {
            echo "⚠️ AI API returned no data for ID: " . $row[$cols[0]] . ". Check your API Key.<br>";
        }
        usleep(500000);
    }
}