
<?php
namespace App;

/**
 * CSRF Protection Class
 * 
 * This class handles CSRF token generation and verification
 * to protect against Cross-Site Request Forgery attacks
 */
class Csrf {
    /**
     * Generate a CSRF token and store it in the session
     * 
     * @return string The generated CSRF token
     */
    public static function generateToken() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Generate a secure random token
        $token = bin2hex(random_bytes(32));
        
        // Store the token in the session
        $_SESSION['csrf_token'] = $token;
        
        return $token;
    }
    
    /**
     * Verify a CSRF token against the one stored in the session
     * 
     * @param string|null $token The token to verify
     * @return bool True if the token is valid, false otherwise
     */
    public static function verifyToken($token) {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if token exists in session
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        // Use hash_equals for timing-safe comparison to prevent timing attacks
        return $token !== null && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Get the current CSRF token or generate a new one
     * 
     * @return string The current CSRF token
     */
    public static function getToken() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Create token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            return self::generateToken();
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Output an HTML hidden input field with the CSRF token
     * 
     * @return string HTML hidden input field
     */
    public static function tokenField() {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
