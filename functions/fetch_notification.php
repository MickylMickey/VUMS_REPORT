<?php
function fetchNotification($conn, $user_id, $user_role, $limit = null, $offset = null)
{
    $notifications = [];

    try {
        // 1. Determine role-based access
        if ($user_role === 'admin') {
            // Admin sees all active notifications across the system
            $whereClause = "WHERE n.is_read IS NOT NULL"; // Dummy true condition or filter
            $params = [];
            $types = "";
        } else {
            // Users/HR only see notifications linked to reports they own
            $whereClause = "WHERE r.user_id = ?";
            $params = [$user_id];
            $types = "s";
        }

        $baseSql = "SELECT 
            n.notification_id AS id,
            n.report_ref_snapshot AS message,
            n.is_read,
            n.created_at,
            CONCAT(sender.user_first_name, ' ', COALESCE(CONCAT(LEFT(sender.user_middle_name, 1), '. '), ''), sender.user_last_name) AS sender_name,
            ur_sender.role_name AS sender_role
        FROM notifications AS n 
        INNER JOIN report AS r ON n.report_id = r.report_id
        LEFT JOIN user_profile AS sender ON sender.user_id = n.sender_id
        LEFT JOIN users AS u_sender ON u_sender.user_id = n.sender_id
        LEFT JOIN user_role AS ur_sender ON u_sender.user_role_id = ur_sender.user_role_id";

        // 2. Combine and Paginate
        $sql = "$baseSql $whereClause ORDER BY n.created_at DESC";

        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int) $limit;
            $params[] = (int) $offset;
            $types .= "ii";
        }

        $stmt = $conn->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    } catch (Exception $e) {
        error_log("Notification Fetch Error: " . $e->getMessage());
        $notifications = [];
    }

    return $notifications;
}



/**
 * Converts a datetime string into a "time ago" format (e.g., "5 mins ago")
 * * @param string $datetime The timestamp from the database (Y-m-d H:i:s)
 * @param bool $full Whether to return a full string or just the most significant unit
 * @return string
 */
function formatRelativeTime($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Calculate weeks and remaining days from the absolute total days
    $weeks = floor($diff->days / 7);
    $days = $diff->d; // Days in the current month segment

    $string = [
        'y' => ['label' => 'year', 'value' => $diff->y],
        'm' => ['label' => 'month', 'value' => $diff->m],
        'w' => ['label' => 'week', 'value' => (int) $weeks],
        'd' => ['label' => 'day', 'value' => $diff->d],
        'h' => ['label' => 'hour', 'value' => $diff->h],
        'i' => ['label' => 'minute', 'value' => $diff->i],
        's' => ['label' => 'second', 'value' => $diff->s],
    ];

    $parts = [];
    foreach ($string as $k => $info) {
        if ($info['value'] > 0) {
            // Special handling: if we show weeks, we often don't want to show 
            // the 'total days' property if it overlaps. 
            // For a simple 'Time Ago', the first match is usually best.
            $parts[] = $info['value'] . ' ' . $info['label'] . ($info['value'] > 1 ? 's' : '');
        }
    }

    if (!$full)
        $parts = array_slice($parts, 0, 1);

    return $parts ? implode(', ', $parts) . ' ago' : 'just now';
}