
# Crypto Shop - PHP Online Store with Cryptocurrency Payments

An e-commerce platform built with PHP that allows customers to purchase products using cryptocurrencies through BTCPay Server integration.

## Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer

## Installation

1. Clone this repository:
```bash
git clone <repository-url>
cd crypto-shop
```

2. Install dependencies using Composer:
```bash
composer install
```

3. Set up the database:
```bash
mysql -u your_username -p < database.sql
```

4. Configure environment variables:
```bash
cp .env.example .env
```
Then edit the `.env` file with your actual credentials.

5. Start the PHP development server:
```bash
cd public
php -S localhost:8000
```

## Adding Test Products

You can add test products to the database using this SQL:

```sql
INSERT INTO products (name, price, description, stock, image_url) VALUES 
('Bitcoin T-Shirt', 25.99, 'High-quality cotton t-shirt with Bitcoin logo.', 100, 'img/products/bitcoin-tshirt.jpg'),
('Ethereum Mug', 15.50, 'Ceramic mug with Ethereum logo.', 75, 'img/products/ethereum-mug.jpg'),
('Crypto Hardware Wallet', 89.99, 'Secure hardware wallet for cryptocurrency.', 30, 'img/products/hardware-wallet.jpg');
```

## Setting Up BTCPay Server

### Overview
This application uses BTCPay Server for processing cryptocurrency payments. To set it up:

1. Create an account on a BTCPay Server instance or set up your own instance (https://btcpayserver.org/)
2. Create a store and connect your cryptocurrency wallets
3. Generate API credentials (API Key and Secret) from your store settings
4. Add these credentials to your `.env` file:
   - `CRYPTO_API_KEY`: Your BTCPay Server API Key
   - `CRYPTO_API_SECRET`: Your BTCPay Server API Secret
   - `CRYPTO_API_URL`: Your BTCPay Server URL (e.g., https://your-btcpay-instance.com)

### Testing Payments
For development purposes, the application includes a simulated payment flow that doesn't require an actual BTCPay Server connection. This allows you to test the checkout process without setting up cryptocurrency wallets.

## Configuring Email Notifications

The application uses PHPMailer to send order confirmation emails. To configure it:

1. Add your email SMTP settings to your `.env` file:
   - `MAIL_HOST`: Your SMTP server (e.g., smtp.gmail.com)
   - `MAIL_USERNAME`: Your email address
   - `MAIL_PASSWORD`: Your email password or app password
   - `MAIL_FROM_NAME`: The sender name that appears in emails

2. If using Gmail:
   - Create an App Password instead of using your account password
   - Go to your Google Account > Security > 2-Step Verification > App passwords
   - Create a new app password and use it for `MAIL_PASSWORD`

3. For local testing, you can use Mailtrap:
   - Create a free Mailtrap account (https://mailtrap.io/)
   - Get the SMTP credentials from your inbox settings
   - Update your `.env` file with these credentials

## Admin Dashboard

### Setup and Access

The application includes an admin dashboard to manage products and view orders. A default admin user is created when you import the database (`username: admin, password: admin123`).

To access the admin dashboard:
1. Navigate to `http://localhost:8000/admin/login.php`
2. Login with the default credentials
3. IMPORTANT: Change the default password after your first login

### Creating Additional Admin Users

You can create additional admin users by executing the following SQL (replace username and password with your desired values):

```sql
INSERT INTO admins (username, password) VALUES 
('your_username', '$2y$10$HKgXlAGF3pJSToEH3WF2LeLwQR.MHI0B9Sm4srQufnK5tDdFQ5n4.');
```

To generate a properly hashed password, you can use PHP's `password_hash()` function:

```php
$hashedPassword = password_hash('your_password', PASSWORD_DEFAULT);
echo $hashedPassword;
```

### Security Features

The admin dashboard includes several security features:
- Rate limiting (blocks login attempts after 5 failed attempts in 5 minutes)
- Session timeout (automatically logs out inactive users after 30 minutes)
- Password hashing using PHP's secure `password_hash()` function
- Content Security Policy headers to prevent XSS attacks
- CSRF protection for all form submissions

To test rate limiting:
1. Attempt to login with incorrect credentials multiple times
2. After 5 failed attempts, you will be temporarily blocked

## Development

To verify your setup:

1. Ensure the database is properly configured:
   - Check that `database.sql` has been imported
   - Verify `.env` contains correct database credentials
   - Test connection by visiting the homepage

2. Access the development server at `http://localhost:8000`

3. Test the checkout flow:
   - Add products to cart
   - Complete the checkout process
   - Observe the simulated cryptocurrency payment flow

## Features

- Product catalog with details
- Shopping cart functionality
- Cryptocurrency payment processing via BTCPay Server
- Order management
- Email notifications
- Admin dashboard for product and order management

## Project Structure

- `/public`: Public-facing files (index.php)
- `/public/admin`: Admin dashboard files
- `/src`: Application source code
- `/src/Models`: Data models
- `/src/Services`: Service classes
- `/views`: Template files
- `/config`: Configuration files

## License

This project is open-source and available under the MIT License.
