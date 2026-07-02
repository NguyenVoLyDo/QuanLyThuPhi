<?php
/**
 * Database Configuration - PDO Connection
 * Cấu hình kết nối cơ sở dữ liệu với PDO
 */

class Database
{
    // Thông tin kết nối
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $charset = 'utf8mb4';

    public $conn;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: (defined('DATABASE') ? DATABASE['Host'] : 'localhost');
        $this->port = getenv('DB_PORT') ?: (defined('DATABASE') ? DATABASE['Port'] : '3306');
        $this->db_name = getenv('DB_NAME') ?: (defined('DATABASE') ? DATABASE['Name'] : 'student_fee_management');
        $this->username = getenv('DB_USER') ?: (defined('DATABASE') ? DATABASE['User'] : 'root');
        $this->password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : (defined('DATABASE') ? DATABASE['Pass'] : '');
    }

    /**
     * Kết nối database với PDO
     * @return PDO|null
     */
    public function connect()
    {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset={$this->charset}";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException $e) {
            echo "Lỗi kết nối: " . $e->getMessage();
            error_log("Database Connection Error: " . $e->getMessage());
        }

        return $this->conn;
    }

    /**
     * Get connection (alias for connect method)
     * @return PDO|null
     */
    public function getConnection()
    {
        if ($this->conn === null) {
            return $this->connect();
        }
        return $this->conn;
    }

    /**
     * Đóng kết nối
     */
    public function disconnect()
    {
        $this->conn = null;
    }
}

/**
 * Helper Functions - Các hàm tiện ích
 */



/**
 * Tạo mã code tự động
 */
function generate_code($prefix, $length = 6)
{
    $timestamp = time();
    $random = str_pad(rand(0, 999999), $length, '0', STR_PAD_LEFT);
    return $prefix . date('Ymd') . $random;
}

/**
 * Kiểm tra quyền truy cập
 */
function check_permission($allowed_roles = [])
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /QuanLyThuPhi/index.php?action=login');
        exit();
    }

    // Check both 'role' and 'role_name' for backward compatibility
    $user_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

    if (!empty($allowed_roles) && !in_array($user_role, $allowed_roles)) {
        header('Location: /QuanLyThuPhi/index.php?action=dashboard&error=no_permission');
        exit();
    }

    return true;
}

/**
 * Flash Message
 */
function set_flash($key, $message, $type = 'success')
{
    $_SESSION['flash'][$key] = [
        'message' => $message,
        'type' => $type
    ];
}

function get_flash($key)
{
    if (isset($_SESSION['flash'][$key])) {
        $flash = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $flash;
    }
    return null;
}
