<?php
require_once "../init.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (
    !isset($data['input']) ||
    !isset($data['suggested']) ||
    !isset($data['actual']) ||
    !isset($data['is_correction']) ||
    !isset($data['field_type'])
) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

$input = trim($data['input']);
$suggested = (int) $data['suggested'];
$actual = (int) $data['actual'];
$isCorrection = (int) $data['is_correction'];
$field = $data['field_type'];

$allowedFields = ['category', 'module', 'severity'];

if (!in_array($field, $allowedFields)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid field type"]);
    exit;
}

$normalized = strtolower($input);

try {
    $stmt = $conn->prepare("
        INSERT INTO ai_learning_logs 
        (user_input, normalized_input, suggested_id, actual_id, field_type, is_correction)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $input,
        $normalized,
        $suggested,
        $actual,
        $field,
        $isCorrection
    ]);

    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "DB error"]);
}
$data = json_decode(file_get_contents('php://input'), true);

echo json_encode($data);
exit;
?>