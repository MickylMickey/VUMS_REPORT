<?php
function
    getEmbedding(
    $text,
    $key
) {
    $modelPath = "models/gemini-embedding-001";

    $url = "https://generativelanguage.googleapis.com/v1beta/{$modelPath}:embedContent?key=" . $key;

    $payload = json_encode([
        "model" => $modelPath,
        "content" => [
            "parts" => [
                ["text" => $text]
            ]
        ]
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);

    if ($response === false) {
        die(curl_error($ch));
    }

    curl_close($ch);

    $res = json_decode($response, true);

    return $res['embedding']['values'] ?? [];
}