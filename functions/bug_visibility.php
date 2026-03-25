<?php

class BugVisibility
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Fetches reports based on user permissions.
     * Admin (Role 1) sees all. Users see only their own.
     */
    public function getVisibleReports(string $currentUserId, int $currentUserRole, ?int $limit = null, ?int $offset = null): array
    {
        $params = [];
        $types = "";

        // Base Query with Joins to get human-readable names
        $sql = "SELECT 
                    r.*, 
                    u.username,
                    c.cat_name, 
                    m.mod_name, 
                    s.sev_name, 
                    st.status_name
                FROM report r
                INNER JOIN users u ON r.user_id = u.user_id
                INNER JOIN category c ON r.cat_id = c.cat_id
                INNER JOIN module m ON r.mod_id = m.mod_id
                INNER JOIN severity s ON r.sev_id = s.sev_id
                INNER JOIN status st ON r.status_id = st.status_id";

        // Logic: If NOT Admin (Role 1), restrict to the user's own reports
        if ($currentUserRole !== 1) {
            $sql .= " WHERE r.user_id = ?";
            $params[] = $currentUserId;
            $types .= "s"; // CHAR(36) is a string
        }

        $sql .= " ORDER BY r.report_created_at DESC";

        // Add Pagination
        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
            $types .= "i";

            if ($offset !== null) {
                $sql .= " OFFSET ?";
                $params[] = $offset;
                $types .= "i";
            }
        }

        return $this->executeQuery($sql, $params, $types);
    }

    /**
     * Helper to execute prepared statements
     */
    private function executeQuery($sql, $params, $types): array
    {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("SQL Prepare Error: " . $this->conn->error);
            return [];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            error_log("SQL Execute Error: " . $stmt->error);
            return [];
        }

        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}