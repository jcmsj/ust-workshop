#!/bin/bash
# Switch to root user
sudo -i

set -e
exec > /var/log/user-data.log 2>&1
# Update package lists and install Git, curl, build-essential, and Apache
apt-get update -y
apt-get install -y git curl build-essential apache2

# Enable Apache rewrite module for Laravel
a2enmod rewrite

# Install PHP 8.3 and required extensions (including intl and zip)
add-apt-repository ppa:ondrej/php -y
apt update -y
apt install -y \
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

# Composer
apt install -y composer;

# Node 22
curl -sL https://deb.nodesource.com/setup_22.x -o nodesource_setup.sh;
bash nodesource_setup.sh;
apt install -y nodejs;

# Clone your TALL stack application repository
cd /var/www
if [ -d "ust-workshop" ]; then
    echo "Removing existing ust-workshop directory";
    rm -rf ust-workshop;
fi

# Set the HOME environment variable to the home directory of the user running the script
export HOME=~;

echo "Cloning repository...";
git clone https://github.com/jcmsj/ust-workshop ust-workshop;
cd ust-workshop;

# Add this directory to Git's safe.directory configuration so Git won't complain about ownership
git config --global --add safe.directory /var/www/ust-workshop;

# If .env doesn't exist, copy .env.example
if [ ! -f .env ]; then
    cp .env.example .env;
fi

# Update the .env file with the RDS details:
sed -i 's/DB_HOST=127.0.0.1/DB_HOST=$DB_HOST/g' .env
sed -i 's/DB_DATABASE=fil/DB_DATABASE=ctp/g' .env
sed -i 's/DB_USERNAME=root/DB_USERNAME=root/g' .env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=ustworkshop/' .env

cd /var/www/ust-workshop;

# Install PHP dependencies with Composer
composer install --no-interaction;

# Run database migrations and seed the database (if desired)
yes | php artisan migrate --force 
# if this is the 2nd instance,  errors will appear since we already seeded (SQLSTATE[23505], duplicate records), just ignore
php artisan db:seed || true;
echo "Database seeded";

# Generate application key (if not already set)
php artisan key:generate

# Clear and cache the Laravel configuration to pick up .env changes
php artisan config:cache

# Install Node dependencies
npm install

# Run npm production build for frontend assets
npm run build

# Change ownership of the project directory to the ubuntu user so Composer can write to it
chown -R ubuntu:ubuntu /var/www/ust-workshop;

# Now that everything is built, fix permissions for storage and cache
chown -R www-data:www-data /var/www/ust-workshop/storage /var/www/ust-workshop/bootstrap/cache
chmod -R 775 /var/www/ust-workshop/storage /var/www/ust-workshop/bootstrap/cache

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
