<?php

function fetchAllFromTable($conn, $table)
{
    // Only allow specific table names to prevent SQL injection
    $allowedTables = ['category', 'module', 'severity'];
    if (!in_array($table, $allowedTables)) {
        return [];
    }

    $sql = "SELECT * FROM `$table`";
    $result = $conn->query($sql);

    if (!$result) {
        error_log("Database error on table $table: " . $conn->error);
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

// How to use it:
$categories = fetchAllFromTable($conn, 'category');
$modules = fetchAllFromTable($conn, 'module');
$severities = fetchAllFromTable($conn, 'severity');
?>