2. Database Design
Design and implement a database schema for:
Users (use Laravel's default user model with Cashier columns)
Locations (id, user_id, name, latitude, longitude)
WeatherAlerts (id, location_id, alert_type, message, created_at)
Use Laravel migrations and seeders to set up the database
Implement eloquent relationships between models

for the project setup:
composer create-project --prefer-dist laravel/laravel weather-alert-app
php artisan key:generate

for the models and migrations:
php artisan make:model Location -m
php artisan make:model WeatherAlert -m

for seeder:
php artisan make:seeder UserSeeder
php artisan make:seeder LocationSeeder

Run Migrations and Seeders:
php artisan migrate
you should run the seeder one next one in order to avoid foreign keys problems issues
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=LocationSeeder
php artisan db:seed --class=WeatherAlertSeeder

or you can seed the three tables writing one command line:
php artisan db:seed

