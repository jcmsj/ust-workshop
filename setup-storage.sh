# Notes on first time installation:
# the storage dir needs:
STORAGE_URL=/var/www/storage;
ANALYTICS=$STORAGE_URL/app/analytics/;
# check if the credentials file exists locally
ACCOUNT_CREDENTIALS=./storage/app/analytics/service-account-credentials.json;
if [ ! -f "$ACCOUNT_CREDENTIALS" ]; then
  echo "Service account credentials file not found at $ACCOUNT_CREDENTIALS";
  exit 1;
fi

echo "Creating storage directories";
ssh root@138.197.130.152 "
  mkdir -p $STORAGE_URL;
  mkdir -p $STORAGE_URL/app/public;
  mkdir -p $STORAGE_URL/app/private;
  mkdir -p $STORAGE_URL/framework;
  mkdir -p $STORAGE_URL/framework/sessions;
  mkdir -p $STORAGE_URL/framework/views;
  mkdir -p $STORAGE_URL/framework/cache;
  mkdir -p $ANALYTICS;
";
echo "Copying Google Analytics Service account credentials";
scp $ACCOUNT_CREDENTIALS root@138.197.130.152:$ANALYTICS/service-account-credentials.json;
ssh root@138.197.130.152 "
  chown -R www-data:www-data $STORAGE_URL;
";
echo "Storage directories created at $STORAGE_URL";
