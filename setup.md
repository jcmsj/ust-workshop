# 
```sh
apt install composer
apt install gh
```

# Get an access token from github
```sh
gh auth login
```
# after successful login, setup git
```sh
gh auth setup-git
```

# install php
```sh
apt install php8.3
```
# Some extensions composer packages need
```sh
apt install php-xml
```
# Switch php version used by apache
```sh
php --info # check current version
# composer i # check what server i am using locally, curently 8.3
sudo a2dismod php8.1  # disables current version of php
sudo a2enmod php8.3  # enables php 8.3
sudo service apache2 restart
apt install php-pgsql
sudo systemctl restart postgresql.service
# Spatie Google analytics
apt install php-bcmath
apt install php-gd

php --info # check current version
# double check, access website
```

# Fix: only home page is working, other pages are not working
## Enable rewrite mode
```sh
sudo a2enmod rewrite
sudo systemctl restart apache2
```
## Update apache config
```sh
nano /etc/apache2/sites-available/000-default.conf
# find the following block
<Directory /var/www/html>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All # change None to All
    Require all granted
</Directory>
```
## Reference
Based on https://stackoverflow.com/a/73548748

