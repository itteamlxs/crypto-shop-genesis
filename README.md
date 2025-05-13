
# Crypto Shop - PHP Online Store with Cryptocurrency Payments

An e-commerce platform built with PHP that allows customers to purchase products using cryptocurrencies through BTCPay Server integration.

## Table of Contents
- [Requirements](#requirements)
- [Installation](#installation)
- [Local Testing](#local-testing)
- [Production Deployment](#production-deployment)
- [Admin Dashboard Setup](#admin-dashboard-setup)
- [BTCPay Server Configuration](#btcpay-server-configuration)
- [Email Configuration](#email-configuration)
- [Security Considerations](#security-considerations)

## Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache or Nginx)

## Installation

### Basic Setup

1. Clone this repository:
```bash
git clone https://github.com/itteamlxs/crypto-shop-genesis.git
cd crypto-shop-genesis
```

2. Install dependencies using Composer:
```bash
composer install
```

3. Set up the environment variables:
```bash
cp .env.example .env
```
Then edit the `.env` file with your actual credentials:
```
# Database Configuration
DB_HOST=localhost     # Host of the MySQL database
DB_NAME=crypto_shop   # Name of the database
DB_USER=your_db_user  # Your database username
DB_PASS=your_db_pass  # Your database password

# BTCPay Server Configuration (see BTCPay Server section below)
CRYPTO_API_KEY=your_api_key
CRYPTO_API_SECRET=your_api_secret
CRYPTO_API_URL=https://your-btcpay-instance.com

# Mail Configuration (see Email Configuration section below)
MAIL_HOST=your_smtp_host
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_email_password
MAIL_FROM_NAME="Crypto Shop"

# Environment Setting
ENVIRONMENT=development  # Change to 'production' when deploying
```

4. Set up the database:
```bash
# Create database and import schema
mysql -u your_username -p < database.sql
```

5. Ensure the web server has proper permissions:
```bash
# Create logs directory and set permissions
mkdir -p logs
chmod 755 logs

# Set permission for product images directory
chmod 755 public/img/products
```

6. Configure your web server:

For Apache:
```apache
<VirtualHost *:80>
    ServerName yoursite.local
    DocumentRoot /path/to/crypto-shop/public
    
    <Directory /path/to/crypto-shop/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

For Nginx:
```nginx
server {
    listen 80;
    server_name yoursite.local;
    root /path/to/crypto-shop/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
    
    location ~ /\. {
        deny all;
    }
}
```

7. Start the PHP development server for testing (alternative to web server setup):
```bash
cd public
php -S localhost:8000
```

### Verifying Installation

After installation, you should:

1. Visit the homepage at `http://localhost:8000` (or your configured domain)
2. Verify that products are displayed correctly
3. Try adding a product to the cart
4. Navigate through the checkout process
5. Login to the admin dashboard at `/admin/login.php` with default credentials:
   - Username: `admin`
   - Password: `admin123`
   
   **Important**: Change these credentials immediately after first login!

## Local Testing

### Configuring BTCPay Server for Testing

1. Use the public BTCPay Server testnet instance:
   - URL: `https://testnet.demo.btcpayserver.org`
   - Update your `.env` file with this URL for `CRYPTO_API_URL`

2. Create a free account on the testnet instance
3. Generate API credentials from your store settings
4. Add these credentials to your `.env` file

### Testing the Payment Flow

1. Install a Bitcoin Testnet wallet:
   - [Electrum](https://electrum.org/) (set to testnet mode)
   - [Bitcoin Testnet Wallet](https://play.google.com/store/apps/details?id=de.schildbach.wallet_test) (Android)

2. Get free testnet bitcoins from faucets:
   - [Bitcoin Testnet Faucet](https://testnet-faucet.mempool.co/)
   - [Coinfaucet](https://coinfaucet.eu/en/btc-testnet/)

3. Use the testing page to simulate a payment:
   - Visit `http://localhost:8000/test_payment.php?product_id=1` (specifying a product ID is optional)
   - This page will generate a test order for the selected product
   - Send the requested amount of testnet bitcoin to the provided address
   - Use the "Check Payment Status" button to verify the payment

### Testing Email Notifications

1. Create a free [Mailtrap](https://mailtrap.io/) account
2. Get the SMTP credentials from your inbox settings
3. Update your `.env` file with Mailtrap credentials:
   ```
   MAIL_HOST=smtp.mailtrap.io
   MAIL_USERNAME=your_mailtrap_username
   MAIL_PASSWORD=your_mailtrap_password
   ```

4. When a payment is confirmed, check your Mailtrap inbox for the confirmation email

## Production Deployment

### Server Requirements

- Ubuntu 20.04 or later
- Nginx or Apache
- PHP 8.1+
- MySQL 5.7+
- SSL certificate (Let's Encrypt)

### Automated Deployment

This repository includes a deployment script for Ubuntu servers with Nginx:

1. Transfer the deployment script to your server:
```bash
scp deploy.sh user@your-server:/tmp/
```

2. SSH into your server:
```bash
ssh user@your-server
```

3. Run the deployment script:
```bash
cd /tmp
chmod +x deploy.sh
sudo ./deploy.sh
```

4. Follow the prompts to configure your domain and database settings

### Manual Deployment

If you prefer to deploy manually:

1. Set up a web server (Nginx or Apache)
2. Configure it to serve the `/public` directory as the document root
3. Ensure all requests are routed through `/public/index.php`
4. Set up the database using the `database.sql` file
5. Configure environment variables in `.env`
6. Set `ENVIRONMENT=production` in the `.env` file

### SSL Configuration

For production, always use HTTPS:

1. With Let's Encrypt:
```bash
sudo certbot --nginx -d yourdomain.com
```

2. Update your Nginx/Apache configuration to force HTTPS

### Security Considerations

1. Ensure all files outside the `/public` directory are not accessible
2. Set proper file permissions (e.g., `755` for directories, `644` for files)
3. Configure your web server with secure headers (already included in `.htaccess` for Apache)
4. Keep PHP and all dependencies updated regularly

## Admin Dashboard Setup

### Creating an Admin User

The database setup includes a default admin user:
- Username: `admin`
- Password: `admin123`

For security, create your own admin user with a strong password:

```sql
-- Generate a password hash (in PHP)
-- $hashedPassword = password_hash('your_secure_password', PASSWORD_DEFAULT);

-- Then execute this SQL with your hash
INSERT INTO admins (username, password) 
VALUES ('your_username', 'your_password_hash');
```

### Accessing the Admin Dashboard

1. Visit `http://localhost:8000/admin/login.php`
2. Login with your admin credentials
3. You'll be redirected to the dashboard where you can:
   - Manage products (create, update, delete)
   - View order information
   - Check payment statuses

### Security Features

The admin dashboard includes several security features:

1. Rate limiting: Blocks login attempts after 5 failed attempts in 5 minutes
2. Session timeout: Automatically logs out inactive users after 30 minutes
3. Password hashing: Uses PHP's secure `password_hash()` function
4. Content Security Policy: Prevents XSS attacks
5. CSRF protection: For all form submissions

## BTCPay Server Configuration

### Setting Up Your Own BTCPay Server

For production use, consider setting up your own BTCPay Server instance:

1. Follow the [BTCPay Server documentation](https://docs.btcpayserver.org/Docker/)
2. Set up your cryptocurrency wallets (Bitcoin, Litecoin, etc.)
3. Configure your domain and SSL certificate
4. Create an API key with appropriate permissions

### Using a Hosted Service

Alternatively, use a BTCPay Server hosting service:

1. Create an account on a BTCPay Server host
2. Set up your store and connect your wallets
3. Generate API credentials (key and secret)
4. Update your `.env` file:
   ```
   CRYPTO_API_KEY=your_api_key
   CRYPTO_API_SECRET=your_api_secret
   CRYPTO_API_URL=https://your-btcpay-instance.com
   ```

### Testing vs Production

- For testing: Use `https://testnet.demo.btcpayserver.org`
- For production: Use your own instance or a hosted solution with mainnet cryptocurrencies

## Email Configuration

### Gmail Configuration

To use Gmail for sending emails:

1. Create a Google account or use an existing one
2. Enable 2-Factor Authentication
3. Generate an App Password:
   - Go to your Google Account > Security > 2-Step Verification
   - Scroll down to App passwords
   - Select "Mail" and "Other" (enter "Crypto Shop")
   - Copy the generated password

4. Update your `.env` file:
   ```
   MAIL_HOST=smtp.gmail.com
   MAIL_USERNAME=your.email@gmail.com
   MAIL_PASSWORD=your_app_password
   MAIL_FROM_NAME="Crypto Shop"
   ```

### Mailtrap for Development

For development environments, use Mailtrap to catch all outgoing emails:

1. Create a [Mailtrap](https://mailtrap.io/) account
2. Get the SMTP credentials
3. Update your `.env` file with these credentials

### Email Testing

To test your email configuration:

1. Use the `MailService::sendTestEmail()` method
2. Create a simple test script:
   ```php
   require_once 'config/bootstrap.php';
   $mailService = new App\Services\MailService();
   $sent = $mailService->sendTestEmail('your-test-email@example.com');
   echo $sent ? 'Test email sent successfully!' : 'Failed to send test email.';
   ```

## Security Considerations

### Security Measures Implemented

Crypto Shop includes a comprehensive set of security measures to protect your application and user data:

1. **Cross-Site Scripting (XSS) Prevention**:
   - All output is escaped using `htmlspecialchars()`
   - Content Security Policy (CSP) headers restrict script sources
   - Input validation on all form submissions

2. **SQL Injection Prevention**:
   - Prepared statements used for all database queries
   - Parameterized queries with proper type binding
   - Input validation before database operations

3. **Cross-Site Request Forgery (CSRF) Protection**:
   - CSRF tokens for all forms and state-changing actions
   - Token verification on form submission
   - Secure token generation using cryptographically secure functions

4. **Session Security**:
   - Secure session configuration (`HttpOnly`, `Secure`, `SameSite=Strict`)
   - Session timeout after 30 minutes of inactivity
   - Session regeneration after login to prevent session fixation
   - Rate limiting for login attempts

5. **Authentication Security**:
   - Strong password hashing with `password_hash()` using bcrypt
   - Rate limiting to prevent brute force attacks
   - Account lockout after multiple failed attempts
   - Secure password requirements

6. **Server Hardening**:
   - HTTPS enforcement with proper SSL/TLS configuration
   - Security headers (CSP, X-Frame-Options, X-Content-Type-Options)
   - Restricted file permissions and directory access
   - Firewall configuration with proper port restrictions
   - Fail2ban integration for intrusion prevention

7. **Error Handling**:
   - Custom error pages to prevent information disclosure
   - Error logging without exposing sensitive information
   - Generic error messages to users
   - Detailed error logging for administrators

8. **Input Validation**:
   - Server-side validation for all form inputs
   - Client-side validation for user experience
   - Strict type checking and data filtering

### Security Recommendations for Production

1. **Access Control and Credentials**:
   - Change the default admin credentials immediately
   - Use strong, unique passwords (12+ characters with mixed types)
   - Implement multi-factor authentication where possible
   - Limit SSH access with key-based authentication only

2. **Regular Maintenance**:
   - Keep all software components updated (`composer update`, `apt update`)
   - Regularly review and rotate API credentials and secrets
   - Monitor server logs for suspicious activity
   - Perform regular security audits

3. **Backups and Recovery**:
   - Set up automated daily database backups
   - Store backups in a separate secure location
   - Test restoration procedure regularly
   - Implement a disaster recovery plan

4. **Additional Security Layers**:
   - Consider adding a Web Application Firewall (WAF)
   - Implement rate limiting on all public-facing endpoints
   - Consider DDoS protection services
   - Set up security monitoring and alerting

5. **Data Protection**:
   - Minimize data collection to what's necessary
   - Don't store sensitive data unnecessarily
   - Encrypt sensitive data at rest
   - Follow applicable data protection regulations (GDPR, CCPA, etc.)

By following these security best practices, you'll maintain a secure environment for your Crypto Shop installation and protect both your business and customer data.

## Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Check your database credentials in `.env`
   - Ensure MySQL service is running
   - Verify that the database exists

2. **Payment Not Being Detected**:
   - Check BTCPay Server credentials
   - Verify that the cryptocurrency network is not congested
   - For testnet, ensure you're using a testnet wallet

3. **Email Not Being Sent**:
   - Check your SMTP settings
   - For Gmail, ensure you're using an App Password
   - Check your mail server logs for any errors

4. **Permission Issues**:
   - Ensure web server has write access to the logs directory
   - Check file ownership (should be web server user)
   - Verify directory permissions (755 for directories, 644 for files)

5. **Blank Page or 500 Error**:
   - Check PHP error logs
   - Temporarily enable error display in development:
     ```php
     ini_set('display_errors', 1);
     error_reporting(E_ALL);
     ```
   - Verify PHP version compatibility (8.1+)

### Getting Help

If you encounter any issues:

1. Check the error logs at `/logs/error.log`
2. Temporarily enable error display for debugging:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
3. Open an issue on the GitHub repository

## License

This project is open-source and available under the MIT License.
