#!/bin/bash
# Log everything
exec > /var/log/user-data.log 2>&1

set -ex  # Enable debug mode & stop on first error

# Update system and install dependencies
apt-get update -y
apt-get install -y git curl build-essential apache2 unzip software-properties-common

# Enable Apache mod_rewrite
a2enmod rewrite

# Install PHP 8.3 and required extensions
add-apt-repository ppa:ondrej/php -y
apt-get update -y
apt-get install -y \
    php8.3 \
    php8.3-cli \
    php8.3-fpm \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-pgsql \
    php8.3-intl \
    php8.3-zip \
    php8.3-curl \
    libapache2-mod-php8.3 

# Install Composer
EXPECTED_SIGNATURE="$(curl -s https://composer.github.io/installer.sig)"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_SIGNATURE="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then
    echo "ERROR: Invalid Composer installer signature" >&2
    rm composer-setup.php
    exit 1
fi

export COMPOSER_HOME="/root/.composer"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php


# Install Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt-get install -y nodejs

# Ensure networking is fully ready
sleep 5

# Clone the Laravel project
cd /var/www
if [ -d "ust-workshop" ]; then
    rm -rf ust-workshop
fi

# Ensure HOME is set before running Git
export HOME=/root

# Retry cloning up to 3 times if it fails
for i in {1..3}; do
    git clone https://github.com/jcmsj/ust-workshop.git ust-workshop && break
    sleep 5
done

# Verify that the repo exists before continuing
if [ ! -d /var/www/ust-workshop ]; then
    echo "❌ ERROR: Git clone failed. Exiting." >&2
    exit 1
fi

cd ust-workshop

export HOME=/root
git config --global --add safe.directory /var/www/ust-workshop

# Set up environment file
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Update .env with correct database credentials
sed -i 's/DB_HOST=127.0.0.1/DB_HOST=localhost/g' .env
sed -i 's/DB_DATABASE=fil/DB_DATABASE=ctp/g' .env
sed -i 's/DB_USERNAME=root/DB_USERNAME=root/g' .env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=ustworkshop/' .env

# Ensure correct permissions before running setup
chown -R ubuntu:ubuntu /var/www/ust-workshop
chmod -R 775 /var/www/ust-workshop/storage /var/www/ust-workshop/bootstrap/cache

# Run Laravel setup as ubuntu user
sudo -u ubuntu -H bash <<EOF
export HOME=/home/ubuntu
export COMPOSER_HOME=/home/ubuntu/.composer
cd /var/www/ust-workshop
composer install
php artisan key:generate
php artisan migrate:fresh --seed --force
php artisan config:cache
npm install
npm run build
EOF


# Fix Apache Permissions
chown -R www-data:www-data /var/www/ust-workshop
chmod -R 755 /var/www/ust-workshop/public
chmod -R 775 /var/www/ust-workshop/storage /var/www/ust-workshop/bootstrap/cache

# Ensure Laravel VirtualHost exists
if [ ! -f /etc/apache2/sites-available/laravel.conf ]; then
    echo "⚠️ laravel.conf is missing! Recreating it..."
    cat <<EOL > /etc/apache2/sites-available/laravel.conf
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    ServerName localhost
    DocumentRoot /var/www/ust-workshop/public

    <Directory /var/www/ust-workshop/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/laravel_error.log
    CustomLog \${APACHE_LOG_DIR}/laravel_access.log combined
</VirtualHost>
EOL
fi

# Ensure Apache loads Laravel instead of default site
a2dissite 000-default.conf || true
a2ensite laravel.conf
sudo systemctl restart apache2

# Final verification
echo "🔎 Checking Apache VirtualHosts..."
sudo apachectl -S

echo "🛠 Checking Laravel directory permissions..."
ls -ld /var/www/ust-workshop/public

echo "✅ If you see 'laravel.conf' in VirtualHosts, you're all set!"
echo "🎯 Deployment finished! Laravel should be live."
