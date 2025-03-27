# Local
LOCAL_TIMESTAMP=$(date +%d-%m-%Y)-$(date +%s);
GIT_REMOTE_URL="https://github.com/jcmsj/ust-workshop.git";
dir="/var/www/$LOCAL_TIMESTAMP";
LINK_LOCATION=/var/www/html;
# This storage directory is used to store uploaded files outside the release directory
STORAGE_URL=/var/www/storage/;
echo "Point release directory will be: $dir";
echo Preparing vite build;
npm run build;
echo "Including .env.production";
cp .env.production public/build/.env;
TAR_FILE=$LOCAL_TIMESTAMP.tar.gz;
tar -czf $TAR_FILE public/build;
scp $TAR_FILE root@IP>:/var/www/

RELEASE_FILE_PATH=/var/www/current_release.txt;
# Put application into maintenance mode
# Server
  # cd \$(cat /var/www/current_release.txt) && php artisan down;
ssh root@<IP> "
  # if current_release.txt does not exist, skip the down command
  [ -f /var/www/current_release.txt ] && cd \$($RELEASE_FILE_PATH) && php artisan down;
  git clone $GIT_REMOTE_URL $dir --depth=1;
  cd $dir;
  rm -rf ./storage;
  rm $LINK_LOCATION;
  ln -s $STORAGE_URL ./storage;
  composer install --no-interaction --prefer-dist --optimize-autoloader;
  tar -xzf ../$TAR_FILE;
  mv public/build/.env .env;
  php artisan filament:optimize;
  php artisan optimize;
  php artisan config:cache;
  chown -R www-data:www-data $dir;
  ln -s $dir/public $LINK_LOCATION;
  rm var/www/$TAR_FILE;
  echo $dir > $RELEASE_FILE_PATH;
  # Bring the application back online
  php artisan up;
"

echo "Deployment complete at $dir";
# create a file specifiying the current release
echo $dir > current_release.txt;

