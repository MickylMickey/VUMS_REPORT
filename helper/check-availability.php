<?php
require_once __DIR__ . "/../config/config.php";

header('Content-Type: application/json');

$field = isset($_GET['field']) ? $_GET['field'] : null;
$value = isset($_GET['value']) ? trim($_GET['value']) : null;
$table = isset($_GET['table']) ? trim($_GET['table']) : null;

// Whitelist allowed fields for security
$allowedFields = ['username', 'email'];

if ($field && $value && $table) {
    // Check if field is in whitelist
    if (!in_array($field, $allowedFields)) {
        echo json_encode(['error' => 'Invalid field']);
        exit;
    }

    // Prepare query safely
    $sql = "SELECT COUNT(*) FROM `$table` WHERE `$field` = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['error' => 'Query preparation failed']);
        exit;
    }

    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    echo json_encode(['exists' => $count > 0]);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>