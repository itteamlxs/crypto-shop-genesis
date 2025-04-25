
# Crypto Shop - PHP Online Store with Cryptocurrency Payments

An e-commerce platform built with PHP that allows customers to purchase products using cryptocurrencies through BTCPay Server integration.

## Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer

## Installation

1. Clone this repository:
```
git clone <repository-url>
cd crypto-shop
```

2. Install dependencies using Composer:
```
composer install
```

3. Set up the database:
```
mysql -u your_username -p < database.sql
```

4. Configure environment variables:
```
cp .env.example .env
```
Then, edit the `.env` file with your actual credentials.

5. Start the PHP development server:
```
cd public
php -S localhost:8000
```

6. Visit `http://localhost:8000` in your browser.

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
