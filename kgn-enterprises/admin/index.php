<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Check brute force
    if (check_brute_force($ip_address, $db)) {
        $error = "Too many failed login attempts. Please try again later.";
    } else {
        try {
            $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = :username AND is_active = 1");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $user['password_hash'])) {
                    // Regenerate session ID
                    session_regenerate_id(true);
                    
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_name'] = $user['full_name'];
                    $_SESSION['admin_role'] = $user['role'];
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['last_activity'] = time();
                    
                    // Update last login
                    $update_stmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = :id");
                    $update_stmt->bindParam(':id', $user['id']);
                    $update_stmt->execute();
                    
                    // Clear failed attempts
                    $clear_stmt = $db->prepare("DELETE FROM login_attempts WHERE ip_address = :ip");
                    $clear_stmt->bindParam(':ip', $ip_address);
                    $clear_stmt->execute();
                    
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Invalid username or password.";
            }
            
            // Log failed attempt
            if ($error) {
                $attempt_stmt = $db->prepare("INSERT INTO login_attempts (ip_address, attempt_time) VALUES (:ip, NOW())");
                $attempt_stmt->bindParam(':ip', $ip_address);
                $attempt_stmt->execute();
            }
            
        } catch(PDOException $e) {
            $error = "Database error. Please try again later.";
            error_log("Admin login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | KGN ENTERPRISES</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #333333 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .input-group input {
            padding-left: 45px;
        }
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }
        .btn-login {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(249, 115, 22, 0.3);
        }
        .security-alert {
            border-left: 4px solid #f97316;
            background: #fffbeb;
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-block p-4 bg-white rounded-2xl shadow-lg mb-4">
                    <img src="../uploads/settings/logo.png" alt="KGN ENTERPRISES" class="h-16 mx-auto">
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">KGN ENTERPRISES</h1>
                <p class="text-gray-300">Admin Panel</p>
            </div>

            <!-- Login Card -->
            <div class="login-card p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Admin Login</h2>
                
                <?php if ($error): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <span class="font-medium"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" action="" id="loginForm">
                    <div class="input-group">
                        <i class="input-icon fas fa-user"></i>
                        <input type="text" 
                               name="username" 
                               required
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                               placeholder="Username"
                               autocomplete="username">
                    </div>

                    <div class="input-group">
                        <i class="input-icon fas fa-lock"></i>
                        <input type="password" 
                               name="password" 
                               required
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                               placeholder="Password"
                               autocomplete="current-password">
                    </div>

                    <button type="submit" 
                            class="btn-login w-full py-3 text-white font-bold rounded-xl mb-4">
                        <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                    </button>

                    <!-- Security Note -->
                    <div class="security-alert p-4 rounded-lg mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-orange-500 mt-1 mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-700 font-medium">
                                    <strong>Security Notice:</strong> This area is restricted to authorized personnel only. All activities are logged.
                                </p>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Company Info -->
                <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-600">
                        &copy; <?php echo date('Y'); ?> KGN ENTERPRISES<br>
                        ISO | MSME | Made in India Certified
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = this.username.value.trim();
            const password = this.password.value.trim();
            
            if (username.length < 3 || password.length < 6) {
                e.preventDefault();
                alert('Please enter valid credentials');
                return false;
            }
            return true;
        });
        
        // Auto focus on username field
        document.querySelector('input[name="username"]').focus();
    </script>
</body>
</html>