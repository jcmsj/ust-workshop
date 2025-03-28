#!/bin/bash
set -e

# Update package lists and install Git, curl, build-essential, and Apache
sudo apt-get update -y
sudo apt-get install -y git curl build-essential apache2

# Enable Apache rewrite module for Laravel
sudo a2enmod rewrite

# Install PHP 8.3 and required extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update -y
sudo apt-get install -y php8.3 php8.3-cli php8.3-fpm php8.3-mbstring php8.3-xml php8.3-pgsql libapache2-mod-php8.3

# Install Composer
EXPECTED_SIGNATURE="$(curl -s https://composer.github.io/installer.sig)"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_SIGNATURE="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then
    >&2 echo 'ERROR: Invalid Composer installer signature'
    rm composer-setup.php
    exit 1
fi
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

# Install Node.js (using NodeSource for Node.js 18)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Clone your TALL stack application repository
cd /var/www
sudo git clone https://github.com/jcmsj/ust-workshop ust-workshop
cd ust-workshop

# If .env doesn't exist, copy .env.example
if [ ! -f .env ]; then
    sudo cp .env.example .env
fi

# Update the .env file with the RDS details:
# Change DB_HOST to the RDS endpoint, update DB_DATABASE, and DB_PASSWORD.
sudo sed -i 's/DB_HOST=127.0.0.1/DB_HOST=ctp.cz0w2yg6aayr.ap-southeast-2.rds.amazonaws.com/g' .env
sudo sed -i 's/DB_DATABASE=fil/DB_DATABASE=ctp/g' .env
sudo sed -i 's/DB_USERNAME=root/DB_USERNAME=root/g' .env
sudo sed -i 's/DB_PASSWORD=root/DB_PASSWORD=ustworkshop/g' .env

# Install PHP dependencies with Composer
sudo composer install

# Run database migrations and seed the database (if desired)
sudo php artisan migrate:fresh --seed || sudo php artisan migrate

# Generate application key (if not already set)
sudo php artisan key:generate

# Clear and cache the Laravel configuration to pick up .env changes
sudo php artisan config:cache

# Optionally, display the database information
sudo php artisan db:show

# Install Node dependencies
sudo npm install

# Run npm production build for frontend assets
sudo npm run build

# Configure Apache Virtual Host for Laravel with a ServerName directive
sudo tee /etc/apache2/sites-available/laravel.conf > /dev/null <<EOL
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

# Verify the file exists
sudo ls -la /etc/apache2/sites-available/laravel.conf

# Disable the default Apache site and enable the Laravel site
sudo a2dissite 000-default.conf
sudo a2ensite laravel.conf

# Reload Apache to apply changes
sudo systemctl reload apache2
