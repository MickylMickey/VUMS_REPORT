<?php

class BugVisibility
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param string $current_user_id 
     * @param string $user_role 
     */
    public function getVisibleReports(string $current_user_id, string $user_role, ?int $limit = null, ?int $offset = null): array
    {
        $params = [];
        $types = "";

        $sql = "SELECT 
            r.*, 
            r.Report_created_at,
            u.username AS reporter_name, 
            updater.username AS updater_name, 
            c.category AS cat_desc,
            m.module AS mod_desc, 
            s.sev_desc AS severity, 
            st.status_desc
        FROM report r
        LEFT JOIN users u ON r.user_id = u.user_id
        LEFT JOIN users updater ON r.updated_by = updater.user_id 
        LEFT JOIN category c ON r.cat_id = c.cat_id
        LEFT JOIN module m ON r.mod_id = m.mod_id
        LEFT JOIN severity s ON r.sev_id = s.sev_id
        LEFT JOIN status st ON r.status_id = st.status_id
        ";

        if ($user_role !== 'Admin') {
            $sql .= " WHERE r.status_id != ? AND r.user_id = ?";
            $params[] = 0;
            $params[] = $current_user_id;
            $types .= "is";
        } else {
            $sql .= " WHERE r.status_id != ?";
            $params[] = 0;
            $types .= "i";
        }

       $sql .= " ORDER BY r.Report_created_at ASC";

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

        // The final return line
        return $this->executeQuery($sql, $params, $types);
    }

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