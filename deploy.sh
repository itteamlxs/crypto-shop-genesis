
#!/bin/bash
# Deployment script for Crypto Shop
# This script sets up the environment on a Ubuntu server with Nginx

# Exit on error
set -e

echo "================================"
echo "  Crypto Shop - Secure Deployment"
echo "================================"
echo 

# Configuration variables - edit these
DOMAIN="example.com"
INSTALL_DIR="/var/www/crypto-shop"
DB_NAME="crypto_shop"
DB_USER="dbuser"
# Generate a secure random password
DB_PASS=$(openssl rand -base64 16)
EMAIL="admin@example.com"  # For Let's Encrypt

# Check if running as root
if [ "$EUID" -ne 0 ]; then
  echo "Please run as root"
  exit 1
fi

echo "Installing dependencies..."
apt update
apt install -y nginx mysql-server php8.1-fpm php8.1-mysql php8.1-curl php8.1-gd php8.1-mbstring php8.1-xml php8.1-zip php8.1-bcmath unzip certbot python3-certbot-nginx git ufw fail2ban

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
mysql -e "GRANT SELECT, INSERT, UPDATE, DELETE ON $DB_NAME.* TO '$DB_USER'@'localhost';"
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

# Create logs directory
mkdir -p $INSTALL_DIR/logs
chmod 755 $INSTALL_DIR/logs

# Create a log rotation configuration
cat > /etc/logrotate.d/crypto-shop << EOF
$INSTALL_DIR/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
EOF

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

# Configure PHP securely
echo "Configuring PHP securely..."
PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
PHP_INI_PATH="/etc/php/$PHP_VERSION/fpm/php.ini"

sed -i 's/allow_url_fopen = On/allow_url_fopen = Off/' $PHP_INI_PATH
sed -i 's/;allow_url_include = Off/allow_url_include = Off/' $PHP_INI_PATH
sed -i 's/display_errors = On/display_errors = Off/' $PHP_INI_PATH
sed -i 's/expose_php = On/expose_php = Off/' $PHP_INI_PATH
sed -i 's/post_max_size = 8M/post_max_size = 8M/' $PHP_INI_PATH
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 2M/' $PHP_INI_PATH

# Restart PHP-FPM
systemctl restart php$PHP_VERSION-fpm

# Create Nginx configuration
echo "Setting up Nginx..."
cat > /etc/nginx/sites-available/crypto-shop << EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root $INSTALL_DIR/public;
    
    index index.php;
    
    # Security headers
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "DENY" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "same-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' https://api.qrserver.com data:; frame-ancestors 'none'" always;
    
    location / {
        try_files \$uri \$uri/ /index.php?\$args;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php$PHP_VERSION-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
        
        # Security settings
        fastcgi_param PHP_VALUE "open_basedir=$INSTALL_DIR/:/tmp/";
    }
    
    # Deny access to dot files
    location ~ /\. {
        deny all;
    }
    
    # Deny access to sensitive files and directories
    location ~* \.(env|log|sql|md|sh)$ {
        deny all;
    }
    
    location ~ ^/(src|vendor|config|logs) {
        deny all;
    }
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/crypto-shop /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration and restart
nginx -t && systemctl restart nginx

# Configure firewall
echo "Setting up firewall..."
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable

# Configure fail2ban
echo "Setting up fail2ban..."
cat > /etc/fail2ban/jail.local << EOF
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true

[nginx-http-auth]
enabled = true

[php-url-fopen]
enabled = true
EOF

systemctl restart fail2ban

# Set up SSL with Let's Encrypt
echo "Setting up SSL..."
certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos -m $EMAIL

# Create a cron job for certificate renewal
echo "0 3 * * * certbot renew --quiet --post-hook 'systemctl reload nginx'" > /etc/cron.d/certbot-renewal
chmod 644 /etc/cron.d/certbot-renewal

echo
echo "================================"
echo "  Secure Deployment Complete!"
echo "================================"
echo
echo "Website URL: https://$DOMAIN"
echo "Admin dashboard: https://$DOMAIN/admin/login.php"
echo
echo "Database Information:"
echo "Name: $DB_NAME"
echo "User: $DB_USER"
echo "Password: $DB_PASS"
echo
echo "IMPORTANT: Save this database password securely!"
echo
echo "Security features implemented:"
echo "✓ HTTPS with Let's Encrypt"
echo "✓ Firewall configured with UFW"
echo "✓ Fail2ban for brute force protection"
echo "✓ PHP securely configured"
echo "✓ Security headers added"
echo "✓ Log rotation set up"
echo
echo "Remember to:"
echo "1. Update API keys in .env file"
echo "2. Configure a secure admin account"
echo "3. Regularly update the system with 'apt update && apt upgrade'"
echo

