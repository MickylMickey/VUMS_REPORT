<?php
function getPaginationData($conn, $table, $limit = 10, $page = 1, $where = "", $params = [], $types = "")
{
    // Sanitize and validate inputs
    $limit = (int) $limit;
    $page = (int) $page;

    if ($limit <= 0) {
        $limit = 10;
    }

    //  Always default to 1 for invalid or zero/negative pages
    if ($page <= 0) {
        $page = 1;
    }

    $offset = ($page - 1) * $limit;
    $totalRecords = 0;

    // Handle null params in WHERE automatically
    if (!empty($params)) {
        foreach ($params as $i => $p) {
            if (is_null($p)) {
                // Replace column = ? with (column = ? OR ? IS NULL)
                if (!empty($where)) {
                    $pattern = '/(\w+\s*=\s*\?)/';
                    $where = preg_replace($pattern, '($1 OR ? IS NULL)', $where, 1);
                    $params[] = null;
                    $types .= substr($types, $i, 1); // duplicate type for null param
                }
            }
        }
    }

    $sql = "SELECT COUNT(*) as total FROM $table";
    if (!empty($where)) {
        $sql .= " WHERE $where";
    }

    error_log("LOOOOOK:" . $sql);

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return null;
    }

    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $totalRecords = $result['total'] ?? 0;
    $stmt->close();

    $totalPages = max(1, ceil($totalRecords / $limit)); //  ensure at least 1 page

    return [
        'limit' => $limit,
        'page' => $page,
        'offset' => $offset,
        'totalRecords' => $totalRecords,
        'totalPages' => $totalPages
    ];
}
?>