
#!/bin/bash
# Deployment script for Crypto Shop
# This script sets up the environment on a Ubuntu server with Nginx

# Exit on error
set -e

echo "================================"
echo "  Crypto Shop - Deployment"
echo "================================"
echo 

# Configuration variables - edit these
DOMAIN="example.com"
INSTALL_DIR="/var/www/crypto-shop"
DB_NAME="crypto_shop"
DB_USER="dbuser"
DB_PASS="strong_password_here"
EMAIL="admin@example.com"  # For Let's Encrypt

# Check if running as root
if [ "$EUID" -ne 0 ]; then
  echo "Please run as root"
  exit 1
fi

echo "Installing dependencies..."
apt update
apt install -y nginx mysql-server php8.1-fpm php8.1-mysql php8.1-curl php8.1-gd php8.1-mbstring php8.1-xml php8.1-zip php8.1-bcmath unzip certbot python3-certbot-nginx git

# Install Composer
if ! command -v composer &> /dev/null; then
    echo "Installing Composer..."
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
fi

# Create database
echo "Setting up database..."
mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Create installation directory
echo "Creating installation directory..."
mkdir -p $INSTALL_DIR

# Clone or update repository
if [ -d "$INSTALL_DIR/.git" ]; then
    echo "Updating repository..."
    cd $INSTALL_DIR
    git pull
else
    echo "Cloning repository..."
    git clone https://github.com/itteamlxs/crypto-shop-genesis.git $INSTALL_DIR
    cd $INSTALL_DIR
fi

# Install dependencies
echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Set up environment file
echo "Setting up environment file..."
if [ ! -f "$INSTALL_DIR/.env" ]; then
    cp $INSTALL_DIR/.env.example $INSTALL_DIR/.env
    sed -i "s/DB_HOST=localhost/DB_HOST=localhost/" $INSTALL_DIR/.env
    sed -i "s/DB_NAME=crypto_shop/DB_NAME=$DB_NAME/" $INSTALL_DIR/.env
    sed -i "s/DB_USER=dbuser/DB_USER=$DB_USER/" $INSTALL_DIR/.env
    sed -i "s/DB_PASS=dbpassword/DB_PASS=$DB_PASS/" $INSTALL_DIR/.env
    echo "ENVIRONMENT=production" >> $INSTALL_DIR/.env
    echo "Environment file created. Please update API keys manually."
fi

# Import database structure
echo "Importing database structure..."
mysql $DB_NAME < $INSTALL_DIR/database.sql

# Set correct permissions
echo "Setting permissions..."
chown -R www-data:www-data $INSTALL_DIR
chmod -R 755 $INSTALL_DIR
chmod -R 775 $INSTALL_DIR/logs

# Create Nginx configuration
echo "Setting up Nginx..."
cat > /etc/nginx/sites-available/crypto-shop << EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root $INSTALL_DIR/public;
    
    index index.php;
    
    location / {
        try_files \$uri \$uri/ /index.php?\$args;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }
    
    location ~ /\.ht {
        deny all;
    }
    
    location ~ /\.git {
        deny all;
    }
    
    # Deny access to sensitive files
    location ~ \.(env|log|sql)$ {
        deny all;
    }
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/crypto-shop /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration and restart
nginx -t && systemctl restart nginx

# Set up SSL with Let's Encrypt
echo "Setting up SSL..."
certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos -m $EMAIL

echo
echo "================================"
echo "  Deployment complete!"
echo "================================"
echo
echo "Website URL: https://$DOMAIN"
echo "Admin dashboard: https://$DOMAIN/admin/login.php"
echo
echo "Remember to:"
echo "1. Update API keys in .env file"
echo "2. Configure a secure admin account"
echo "3. Set up cronjobs for maintenance tasks"
echo
