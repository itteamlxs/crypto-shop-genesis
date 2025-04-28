
<?php
namespace App\Services;

use App\Models\Order;
use BTCPayServer\Client\Invoice;
use BTCPayServer\Client\Client;

/**
 * Payment service class for BTCPay Server integration
 * Handles cryptocurrency payment processing
 */
class PaymentService {
    private $client;
    private $apiKey;
    private $apiUrl;
    private $isTestnet;
    
    /**
     * Constructor - Initialize BTCPay Server client
     */
    public function __construct() {
        $this->apiKey = $_ENV['CRYPTO_API_KEY'] ?? '';
        $this->apiUrl = $_ENV['CRYPTO_API_URL'] ?? 'https://btcpay.example.com/';
        $this->isTestnet = strpos($this->apiUrl, 'testnet') !== false;
        
        // In a production environment, we would initialize the BTCPay client here
        // For example:
        // $this->client = new Client($this->apiUrl, $this->apiKey);
        
        // For this implementation, we'll simulate the API responses
    }
    
    /**
     * Create an invoice for payment
     * 
     * @param float $amount Amount to pay
     * @param string $email Customer email (optional)
     * @param string $productName Product name for reference
     * @return array|false Payment data or false on failure
     */
    public function createInvoice($amount, $email = null, $productName = 'Product') {
        // In a production environment, we would call the BTCPay Server API
        // For example:
        // $invoice = new Invoice($this->client);
        // $invoiceData = $invoice->createInvoice([
        //     'amount' => $amount,
        //     'currency' => 'USD',
        //     'metadata' => [
        //         'buyerEmail' => $email,
        //         'orderId' => uniqid('order_'),
        //         'itemDesc' => $productName
        //     ]
        // ]);
        
        // Simulate creating a BTCPay invoice
        // Generate a fake Bitcoin address with testnet prefix if in testnet mode
        $bitcoinAddress = $this->isTestnet ? 'm' . bin2hex(random_bytes(20)) : '1' . bin2hex(random_bytes(20));
        
        return [
            'id' => uniqid('inv_'),
            'address' => $bitcoinAddress,
            'amount_btc' => $this->convertToBtc($amount),
            'amount_usd' => $amount,
            'expiration_time' => time() + 3600, // 1 hour expiration
            'status' => 'pending',
            'payment_url' => "bitcoin:$bitcoinAddress?amount=" . $this->convertToBtc($amount),
            'is_testnet' => $this->isTestnet
        ];
    }
    
    /**
     * Check payment status
     * 
     * @param string $invoiceId BTCPay Server invoice ID
     * @return string Payment status ('pending', 'paid', 'expired')
     */
    public function checkPaymentStatus($invoiceId) {
        // In a production environment, we would call the BTCPay Server API
        // For example:
        // $invoice = new Invoice($this->client);
        // $invoiceData = $invoice->getInvoice($invoiceId);
        // return $invoiceData['status'];
        
        // Simulate checking payment status
        // For demonstration purposes, randomly return a status
        $statuses = ['pending', 'paid', 'expired'];
        return $statuses[array_rand($statuses)];
    }
    
    /**
     * Confirm payment for an order
     * 
     * @param int $orderId Order ID
     * @return bool True if payment confirmed, false otherwise
     */
    public function confirmPayment($orderId) {
        // Get order details
        $order = \App\Models\Order::getById($orderId);
        
        if (!$order) {
            return false;
        }
        
        // Check payment status with BTCPay Server
        $status = $this->checkPaymentStatus($orderId);
        
        // If payment is confirmed, update order status
        if ($status === 'paid' && $order['status'] !== 'paid') {
            // Update order status
            \App\Models\Order::updateStatus($orderId, 'paid');
            
            // Send confirmation email if email is provided
            if (!empty($order['email']) && class_exists('\App\Services\MailService')) {
                $mailService = new \App\Services\MailService();
                $product = \App\Models\Product::getById($order['product_id']);
                
                $paymentData = [
                    'address' => $order['crypto_address'],
                    'amount_btc' => $this->convertToBtc($order['amount']),
                    'amount_usd' => $order['amount']
                ];
                
                $mailService->sendOrderConfirmation(
                    $order['email'],
                    $orderId,
                    $product['name'] ?? 'Product',
                    $paymentData
                );
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Generate QR code URL for payment
     * 
     * @param string $paymentUrl Payment URL
     * @return string QR code URL
     */
    public function generateQrCode($paymentUrl) {
        // Use a third-party service to generate QR code
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($paymentUrl);
    }
    
    /**
     * Convert USD to BTC (simulated)
     * 
     * @param float $usdAmount Amount in USD
     * @return float Amount in BTC
     */
    private function convertToBtc($usdAmount) {
        // Simulate exchange rate - higher for testnet to make amounts more visible
        $exchangeRate = $this->isTestnet ? 10000 : 50000; // 1 BTC = $10,000 in testnet, $50,000 in mainnet
        return round($usdAmount / $exchangeRate, 8);
    }
    
    /**
     * Check if running in testnet mode
     * 
     * @return bool True if in testnet mode
     */
    public function isTestnet() {
        return $this->isTestnet;
    }
}
