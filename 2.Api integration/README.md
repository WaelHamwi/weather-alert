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

1. API Consumption
Integrate with a weather API (e.g., OpenWeatherMap, WeatherAPI.com)
Fetch current weather and hourly forecasts for user-specified locations
Use Laravel's HTTP client for API requests
Implement proper error handling for API calls

let us visit the website first :[OpenWeatherMap](https://home.openweathermap.org/)

Set Up Environment Variables:
Add your API key and base URL to your .env file:
WEATHER_API_KEY=your_api_key_here
WEATHER_API_URL=https://api.openweathermap.org/data/2.5

then we register the service in the app/Services:
Create a service class to interact with the weather API

then we use the Service in our controller by invoking the methods
// i did not use the design pattern for the controller cause the project is mini and does not need the design pattern approach

php artisan make:controller  WeatherController

now handle the login process to let the auth users only get into the app and excute the methods:
php artisan make:controller Auth/LoginController
