## Settings
1. Make a new settings class using
```
php artisan make:setting SettingName --group=groupName 
```
2. Now, you will have to add this settings class to the [settings.php](./config/settings.php) config file in the settings array, so it can be loaded by Laravel:
```php
  /*
    * Each settings class used in your application must be registered, you can
    * add them (manually) here.
    */
  'settings' => [
      GeneralSettings::class
  ],
```
3. Each property in a settings class needs a default value that should be set in its migration. You can create a migration as such:
```
php artisan make:settings-migration CreateGeneralSettings
```
4. Once you've created your settings class, you can create a settings page in Filament for it using the following command:
```
php artisan make:filament-settings-page ManageFooter FooterSettings
```

## Using S3/Digital Ocean Spaces configuration

1. In the configuration page of the bucket, need to set the Allowed Headers to "*" for a given domain
2. Allow all HTTP methods

## Using Neon Postgres
1. Need to use a non-pooling connection for migrations and cache


## HTTPS Error w/ Filament
1. Must set ASSET_URL to be the same as APP_URL in the .env file

## Doing migrations in Prod:
1. specify the env
2. use the correct connection (e.g., nonpooler url for neon)
```
php artisan migrate --env=production --database=pgsql-cache
```
