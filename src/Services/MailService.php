
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
        
        // Configure SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['MAIL_HOST'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['MAIL_USERNAME'];
        $this->mailer->Password = $_ENV['MAIL_PASSWORD'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        
        // Set sender
        $this->mailer->setFrom($_ENV['MAIL_USERNAME'], $_ENV['MAIL_FROM_NAME']);
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
                <p>Please send {$paymentData['amount_btc']} BTC to the following address:</p>
                <p><strong>{$paymentData['address']}</strong></p>
                <p>Payment expires in 1 hour.</p>
                <p>You can check your order status at: <a href='http://localhost:8000/order/status/{$orderId}'>http://localhost:8000/order/status/{$orderId}</a></p>
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
}
