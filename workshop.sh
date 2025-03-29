#!/bin/bash
sudo -i

set -e

apt-get update -y
apt-get install -y git curl build-essential apache2

a2enmod rewrite

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

curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt-get install -y nodejs

cd /var/www
if [ -d "ust-workshop" ]; then
    echo "Removing existing ust-workshop directory"
    rm -rf ust-workshop
fi
echo "Cloning repository..."
git clone https://github.com/jcmsj/ust-workshop ust-workshop
cd ust-workshop

git config --global --add safe.directory /var/www/ust-workshop

if [ ! -f .env ]; then
    cp .env.example .env
fi

sed -i 's/DB_HOST=127.0.0.1/DB_HOST=$DB_HOST/g' .env
sed -i 's/DB_DATABASE=fil/DB_DATABASE=ctp/g' .env
sed -i 's/DB_USERNAME=root/DB_USERNAME=root/g' .env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=ustworkshop/' .env

chown -R ubuntu:ubuntu /var/www/ust-workshop

exit

cd /var/www/ust-workshop

composer install

composer update

yes | php artisan migrate:fresh --seed

php artisan key:generate

php artisan config:cache

npm install

npm run build

sudo -i

chown -R www-data:www-data /var/www/ust-workshop/storage /var/www/ust-workshop/bootstrap/cache
chmod -R 775 /var/www/ust-workshop/storage /var/www/ust-workshop/bootstrap/cache

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

ls -la /etc/apache2/sites-available/laravel.conf

a2dissite 000-default.conf
a2ensite laravel.conf

systemctl reload apache2
