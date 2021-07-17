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
6. Access Swagger
url = {APP_URL}/api/documentation

7. Use the below credentials for Register
name = Test
email = test@aspire.com
password = secret

8. Use the below credentials for Login
email = test@aspire.com
password = secret