<?php
require_once "../init.php";
$apiKey = $_ENV['GOOGLE_API_KEY'];

// =========================
// 1. GET HIGH-FREQUENCY CORRECTIONS
// =========================
$sql = "
SELECT 
    normalized_input,
    actual_id,
    field_type,
    COUNT(*) as occurrence
FROM ai_learning_logs
WHERE is_correction = 1
GROUP BY normalized_input, actual_id, field_type
HAVING occurrence >= 3
";

$result = $conn->query($sql);
$rows = $result->fetch_all(MYSQLI_ASSOC);

if (!$rows) {
    echo "No learning data.";
    exit;
}

// =========================
// 2. PROCESS EACH LEARNED PATTERN
// =========================
foreach ($rows as $row) {

    $phrase = $row['normalized_input'];
    $entityId = $row['actual_id'];
    $field = $row['field_type'];
    $weight = $row['occurrence'];

    // =========================
    // 3. STORE LEARNED PATTERN
    // =========================
    $insert = $conn->prepare("
        INSERT INTO ai_learned_patterns (phrase, entity_id, field_type, weight)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE weight = weight + VALUES(weight)
    ");

    $insert->bind_param("sisi", $phrase, $entityId, $field, $weight);
    $insert->execute();

    // =========================
    // 4. GET BASE DESCRIPTION (DYNAMIC TABLE)
    // =========================
    $baseDesc = "";

    if ($field === 'category') {
        $baseStmt = $conn->prepare("SELECT cat_desc FROM category WHERE cat_id = ?");
    } elseif ($field === 'module') {
        $baseStmt = $conn->prepare("SELECT mod_desc FROM module WHERE mod_id = ?");
    } elseif ($field === 'severity') {
        $baseStmt = $conn->prepare("SELECT sev_desc FROM severity WHERE sev_id = ?");
    }

    if (isset($baseStmt)) {
        $baseStmt->bind_param("i", $entityId);
        $baseStmt->execute();
        $baseResult = $baseStmt->get_result();
        $baseDesc = $baseResult->fetch_assoc()[array_key_first($baseResult->fetch_fields())] ?? '';
    }

    // =========================
    // 5. GET LEARNED PATTERNS
    // =========================
    $learnedStmt = $conn->prepare("
        SELECT phrase, weight 
        FROM ai_learned_patterns 
        WHERE entity_id = ? AND field_type = ?
    ");

    $learnedStmt->bind_param("is", $entityId, $field);
    $learnedStmt->execute();
    $learnedResult = $learnedStmt->get_result();

    $extraText = "";

    while ($p = $learnedResult->fetch_assoc()) {
        $extraText .= str_repeat($p['phrase'] . " ", min($p['weight'], 3));
    }

    $finalText = $baseDesc . " " . $extraText;

    // =========================
    // 6. GENERATE EMBEDDING
    // =========================
    $newVector = getEmbedding($finalText, $apiKey);

    // =========================
    // 7. UPDATE VECTOR (DYNAMIC TABLE)
    // =========================
    $vectorString = json_encode($newVector);

    if ($field === 'category') {

        $save = $conn->prepare("
            UPDATE category 
            SET cat_vector = ?, cat_updated_at = NOW()
            WHERE cat_id = ?
        ");
        $save->bind_param("si", $vectorString, $entityId);
        $save->execute();

    } elseif ($field === 'module') {

        $save = $conn->prepare("
            UPDATE module 
            SET mod_vector = ?, mod_updated_at = NOW()
            WHERE mod_id = ?
        ");
        $save->bind_param("si", $vectorString, $entityId);
        $save->execute();

    } elseif ($field === 'severity') {

        $save = $conn->prepare("
            UPDATE severity 
            SET sev_vector = ?, sev_updated_at = NOW()
            WHERE sev_id = ?
        ");
        $save->bind_param("si", $vectorString, $entityId);
        $save->execute();
    }
}

echo "Auto-train complete: " . count($rows) . " patterns learned.";
?>