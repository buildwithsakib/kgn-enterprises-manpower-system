<?php

class Database {
    private $host = "localhost";
    private $db_name = "kgn_enterprises";
    private $username = "your_user";
    private $password = "your_pass";
    public $conn;
    private $is_connected = false;

    public function getConnection() {
        if ($this->is_connected) {
            return $this->conn;
        }
        
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Prevent SQL injection
            $this->conn->exec("SET NAMES utf8mb4");
            $this->conn->exec("SET CHARACTER SET utf8mb4");
            $this->conn->exec("SET time_zone = '+05:30'");
            
            $this->is_connected = true;
            
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            return null;
        }
        
        return $this->conn;
    }
}

// Create database instance
$database = new Database();
$db = $database->getConnection();



function validate_phone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function check_brute_force($ip_address, $db) {
    $now = time();
    $valid_attempts = $now - (2 * 60 * 60); // Last 2 hours
    $valid_attempts_str = date('Y-m-d H:i:s', $valid_attempts);

    $query = "SELECT COUNT(*) as attempts FROM login_attempts 
              WHERE ip_address = :ip_address AND attempt_time > :valid_attempts";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':ip_address', $ip_address);
    $stmt->bindParam(':valid_attempts', $valid_attempts_str);    
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return ($result['attempts'] > 5); // More than 5 attempts in 2 hours
}
// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true);
}
if (!function_exists('sanitize_input')) {
    function sanitize_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}

// Validate email
if (!function_exists('validate_email')) {
    function validate_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
?>