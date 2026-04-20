<?php
function getUnreadCount($conn, $user_id, $role)
{
    if ($role === 'admin') {
        // Admins count everything where receiver is NULL (global) OR specifically them
        $sql = "SELECT COUNT(*) as total FROM notifications 
                WHERE (receiver_id IS NULL OR receiver_id = ?) AND is_read = 0";
    } else {
        // Users only count notifications for reports they OWN
        $sql = "SELECT COUNT(*) as total FROM notifications n 
                INNER JOIN report r ON n.report_id = r.report_id 
                WHERE r.user_id = ? AND n.is_read = 0";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // Use "i" for integer ID
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'] ?? 0;
}

function fetchRecentNotifications($conn, $user_id, $role, $limit = 5)
{
    if ($role === 'admin') {
        // Admin logic: Look for NULL (global) or direct messages
        $whereClause = "WHERE (n.receiver_id IS NULL OR n.receiver_id = ?) AND n.is_deleted = 0";
    } else {
        // User logic: Join reports to ensure they only see their own
        $whereClause = "INNER JOIN report r ON n.report_id = r.report_id 
                        WHERE r.user_id = ? AND n.is_deleted = 0";
    }

    $sql = "SELECT n.*, sender.user_first_name, sender.user_prof as image_path 
            FROM notifications n
            LEFT JOIN user_profile sender ON n.sender_id = sender.user_id
            $whereClause
            ORDER BY n.created_at DESC 
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
function timeAgo($timestamp)
{
    $time = is_numeric($timestamp) ? $timestamp : strtotime($timestamp);
    $current_time = time();
    $diff = $current_time - $time;

    if ($diff < 1)
        return 'Just now';

    $intervals = [
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    ];

    foreach ($intervals as $secs => $label) {
        if ($diff >= $secs) {
            $count = floor($diff / $secs);
            return $count . ' ' . $label . ($count > 1 ? 's' : '') . ' ago';
        }
    }
}
