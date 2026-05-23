<?php
class Database {
    private $host = "localhost";
    private $db_name = "wmsu_union_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->ensureSiteSettingsTable();
        } catch(PDOException $exception) {
            // Keep rendering alive when DB is unreachable.
            $this->conn = null;
        }
        return $this->conn;
    }

    private function ensureSiteSettingsTable() {
        if (!$this->conn) {
            return;
        }

        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS `site_settings` (
                `id` int(11) NOT NULL,
                `site_name` varchar(255) NOT NULL,
                `logo_path` varchar(255) NOT NULL,
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci");

            $countStmt = $this->conn->query("SELECT COUNT(*) FROM site_settings WHERE id = 1");
            if ($countStmt && (int) $countStmt->fetchColumn() === 0) {
                $insertStmt = $this->conn->prepare("INSERT INTO site_settings (id, site_name, logo_path) VALUES (1, ?, ?)");
                $insertStmt->execute([
                    'Faculty Union',
                    'images/facultyunion.png'
                ]);
            }
        } catch(PDOException $exception) {
            // Leave the connection usable even if the table cannot be created.
        }
    }
}
?>