
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

## Project Structure

- `/public`: Public-facing files (index.php)
- `/src`: Application source code
- `/views`: Template files
- `/config`: Configuration files

## License

This project is open-source and available under the MIT License.
