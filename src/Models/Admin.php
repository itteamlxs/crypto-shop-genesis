
<?php
namespace App\Models;

use App\Database;

/**
 * Admin model class
 * Handles admin authentication and security
 */
class Admin {
    /**
     * Authenticate an admin user
     * 
     * @param string $username Admin username
     * @param string $password Admin password (plaintext)
     * @return bool Authentication success
     */
    public static function login($username, $password) {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM admins WHERE username = ?", [$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Start session if not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            // Store admin data in session (excluding password)
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_last_activity'] = time();
            
            // Clear login attempts for this IP
            self::clearLoginAttempts($_SERVER['REMOTE_ADDR']);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if current user is logged in as admin
     * 
     * @return bool True if logged in, false otherwise
     */
    public static function isLoggedIn() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if admin is logged in and session hasn't expired
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            // Check for session timeout (30 minutes)
            if (time() - $_SESSION['admin_last_activity'] > 1800) {
                self::logout();
                return false;
            }
            
            // Update last activity time
            $_SESSION['admin_last_activity'] = time();
            return true;
        }
        
        return false;
    }
    
    /**
     * Log out current admin
     * 
     * @return void
     */
    public static function logout() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Unset session variables
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_last_activity']);
        
        // Destroy session
        session_destroy();
    }
    
    /**
     * Record a failed login attempt
     * 
     * @param string $ipAddress IP address of the request
     * @return void
     */
    public static function recordLoginAttempt($ipAddress) {
        $db = Database::getInstance();
        $db->query("INSERT INTO login_attempts (ip_address) VALUES (?)", [$ipAddress]);
    }
    
    /**
     * Clear login attempts for an IP address
     * 
     * @param string $ipAddress IP address to clear
     * @return void
     */
    public static function clearLoginAttempts($ipAddress) {
        $db = Database::getInstance();
        $db->query("DELETE FROM login_attempts WHERE ip_address = ?", [$ipAddress]);
    }
    
    /**
     * Clean old login attempts (older than 24 hours)
     * 
     * @return void
     */
    public static function cleanOldLoginAttempts() {
        $db = Database::getInstance();
        $db->query(
            "DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        );
    }
    
    /**
     * Check if IP address is blocked due to too many login attempts
     * 
     * @param string $ipAddress IP address to check
     * @param int $maxAttempts Maximum number of attempts allowed
     * @param int $timeframeMinutes Timeframe in minutes to consider
     * @return bool True if blocked, false otherwise
     */
    public static function isIpBlocked($ipAddress, $maxAttempts = 5, $timeframeMinutes = 5) {
        // Clean old login attempts first
        self::cleanOldLoginAttempts();
        
        $db = Database::getInstance();
        $stmt = $db->query(
            "SELECT COUNT(*) as attempt_count FROM login_attempts 
             WHERE ip_address = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [$ipAddress, $timeframeMinutes]
        );
        
        $result = $stmt->fetch();
        return $result['attempt_count'] >= $maxAttempts;
    }
    
    /**
     * Create a new admin user
     * 
     * @param string $username Admin username
     * @param string $password Admin password (plaintext, will be hashed)
     * @return bool Success status
     */
    public static function createAdmin($username, $password) {
        $db = Database::getInstance();
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $db->query(
            "INSERT INTO admins (username, password) VALUES (?, ?)",
            [$username, $hashedPassword]
        );
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Change admin password
     * 
     * @param int $adminId Admin ID
     * @param string $currentPassword Current password (plaintext)
     * @param string $newPassword New password (plaintext, will be hashed)
     * @return bool Success status
     */
    public static function changePassword($adminId, $currentPassword, $newPassword) {
        $db = Database::getInstance();
        
        // Get admin data
        $stmt = $db->query("SELECT * FROM admins WHERE id = ?", [$adminId]);
        $admin = $stmt->fetch();
        
        if (!$admin || !password_verify($currentPassword, $admin['password'])) {
            return false;
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update password
        $stmt = $db->query(
            "UPDATE admins SET password = ? WHERE id = ?",
            [$hashedPassword, $adminId]
        );
        
        return $stmt->rowCount() > 0;
    }
}
