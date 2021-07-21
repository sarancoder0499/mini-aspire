# Mini Aspire Engineering

Please follow the below steps

1. Install third party packages from composer.json
```sh
$ composer install
```
2. Create .env
```sh
$ cp .env.example .env
```
3. Create database (aspire) and change the DB details in .env file
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=aspire
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
```
4. Generate application key
```
$ php artisan key:generate
```
5. Migrate and seed database using
```sh
$ php artisan migrate
```
6. Generate API Documentation
```
php artisan l5-swagger:generate
```
7. Access Swagger
```
url = {APP_URL}/api/documentation
```
8. Use the below credentials for Register
```
name = Test
email = test@aspire.com
password = secret
```
9. Run artisan command to get Client ID and Client Secret
```
php artisan passport:install
```
Copy Password grant Client ID and Client Secret and store in secure place.

10. Click Authorize button and feed given below inputs
```
username = test@aspire.com
password = secret
Client ID = We copied from the previous step
Client Secret = We copied from the previous step
```
11. Create database (aspire_testing) and change the DB details in .env.testing file
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=aspire_testing
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
```
12. Run PHPUnit Testing
```
vendor/bin/phpunit
```
13. Coding Standard as per PHP Code Sniffer, run this code in root folder
```
vendor/bin/phpcs --standard=PSR12 app
```
14. Coding scan completed as per PHP Stan, run this code in root folder ( Known issues 6 )
```
vendor/bin/phpstan analyse app
```