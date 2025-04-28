
<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Service for sending emails
 */
class MailService {
    private $mailer;
    
    /**
     * Constructor - Initialize PHPMailer
     */
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Configure SMTP settings based on the mail host
        $host = $_ENV['MAIL_HOST'] ?? '';
        $username = $_ENV['MAIL_USERNAME'] ?? '';
        $password = $_ENV['MAIL_PASSWORD'] ?? '';
        $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'Crypto Shop';
        
        $this->mailer->isSMTP();
        $this->mailer->Host = $host;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $username;
        $this->mailer->Password = $password;
        
        // Special configuration for Mailtrap
        if ($host === 'smtp.mailtrap.io') {
            $this->mailer->Port = 2525; // Mailtrap's default port
            $this->mailer->SMTPSecure = ''; // Mailtrap doesn't require encryption for testing
        } else {
            // Default configuration for other providers like Gmail
            $this->mailer->Port = 587;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        
        // Set sender
        $this->mailer->setFrom($username, $fromName);
        
        // Enable debugging in development environment
        if (isset($_ENV['ENVIRONMENT']) && $_ENV['ENVIRONMENT'] !== 'production') {
            $this->mailer->SMTPDebug = 2; // Debug output
            $this->mailer->Debugoutput = 'html'; // Format debug output
        }
    }
    
    /**
     * Send order confirmation email
     * 
     * @param string $to Recipient email
     * @param int $orderId Order ID
     * @param string $productName Product name
     * @param array $paymentData Payment data
     * @return bool Success status
     */
    public function sendOrderConfirmation($to, $orderId, $productName, $paymentData) {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "Order Confirmation #{$orderId}";
            
            // Email body
            $body = "
                <h1>Thank you for your order!</h1>
                <p>Order ID: {$orderId}</p>
                <p>Product: {$productName}</p>
                <h2>Payment Information</h2>
                <p>Amount: \${$paymentData['amount_usd']} USD ({$paymentData['amount_btc']} BTC)</p>
                <p>Payment Address: {$paymentData['address']}</p>
                <p>Payment Date: " . date("Y-m-d H:i:s") . "</p>
                <p>Your order has been confirmed and is now being processed.</p>
                <p>You can check your order status at: <a href='http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "/order/status/{$orderId}'>View Order Status</a></p>
                <hr>
                <p>Thank you for shopping with Crypto Shop!</p>
            ";
            
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Mail error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send a test email to verify configuration
     * 
     * @param string $to Recipient email
     * @return bool Success status
     */
    public function sendTestEmail($to) {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "Crypto Shop - Email Configuration Test";
            
            $body = "
                <h1>Email Configuration Test</h1>
                <p>This is a test email to verify that your email configuration is working correctly.</p>
                <p>If you're receiving this, it means your email settings are configured properly!</p>
                <hr>
                <p>Sent from: " . ($_SERVER['HTTP_HOST'] ?? 'Crypto Shop') . "</p>
                <p>Time: " . date("Y-m-d H:i:s") . "</p>
            ";
            
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Test mail error: " . $e->getMessage());
            return false;
        }
    }
}
