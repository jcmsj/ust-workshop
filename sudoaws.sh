#!/bin/bash
# Switch to root user
sudo -i

set -e

# Update package lists and install Git, curl, build-essential, and Apache
apt-get update -y
apt-get install -y git curl build-essential apache2

# Enable Apache rewrite module for Laravel
a2enmod rewrite

# Install PHP 8.3 and required extensions (including intl and zip)
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
    >&2 echo 'ERROR: Invalid Composer installer signature'
    rm composer-setup.php
    exit 1
fi
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

# Install Node.js (using NodeSource for Node.js 18)
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt-get install -y nodejs

# Clone your TALL stack application repository
cd /var/www
if [ -d "ust-workshop" ]; then
    echo "Removing existing ust-workshop directory"
    rm -rf ust-workshop
fi
echo "Cloning repository..."
git clone https://github.com/jcmsj/ust-workshop ust-workshop
cd ust-workshop

# Add this directory to Git's safe.directory configuration so Git won't complain about ownership
git config --global --add safe.directory /var/www/ust-workshop

# If .env doesn't exist, copy .env.example
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Update the .env file with the RDS details:
sed -i 's/DB_HOST=127.0.0.1/DB_HOST=ctp.cz0w2yg6aayr.ap-southeast-2.rds.amazonaws.com/g' .env
sed -i 's/DB_DATABASE=fil/DB_DATABASE=ctp/g' .env
sed -i 's/DB_USERNAME=root/DB_USERNAME=root/g' .env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=ustworkshop/' .env

# Change ownership of the project directory to the ubuntu user so Composer can write to it
chown -R ubuntu:ubuntu /var/www/ust-workshop

# Exit from root to run Composer as ubuntu
exit

# Now, as the ubuntu user, change to the project directory
cd /var/www/ust-workshop

# Install PHP dependencies with Composer (as root, ownership is not an issue)
composer install 

# Composer update
composer update

# Run database migrations and seed the database (if desired)
yes | php artisan migrate:fresh --seed

# Generate application key (if not already set)
php artisan key:generate

# Clear and cache the Laravel configuration to pick up .env changes
php artisan config:cache

# Optionally, display the database information (ignore errors if not available)
php artisan db:show || true

# Install Node dependencies
npm install

# Run npm production build for frontend assets
npm run build

# Configure Apache Virtual Host for Laravel with a ServerName directive and ServerAlias *
tee /etc/apache2/sites-available/laravel.conf > /dev/null <<EOL
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    ServerName localhost
    ServerAlias *
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

# Verify the file exists
ls -la /etc/apache2/sites-available/laravel.conf

# Disable the default Apache site and enable the Laravel site
a2dissite 000-default.conf
a2ensite laravel.conf

# Reload Apache to apply changes
systemctl reload apache2
