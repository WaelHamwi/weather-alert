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

php artisan make:controller WeatherController

now handle the login process to let the auth users only get into the app and excute the methods:
php artisan make:controller Auth/LoginController

3. Stripe Integration with Laravel Cashier
   Use Laravel Cashier for handling subscriptions
   Implement Stripe Checkout for subscription sign-ups
   Integrate Stripe Customer Portal for subscription management
   Offer monthly and annual subscription plans
   Provide a 7-day free trial for new users
   Handle subscription creation, cancellation, and renewal
   Implement webhooks to handle Stripe events (successful payments, subscription status
   changes)

first of all we visit the documentation of laravel to handle the process of using laravel cashier:
https://laravel.com/docs/10.x/billing

composer require laravel/cashier
Next, set your Stripe API keys in your .env file. You'll get these keys from your :https://dashboard.stripe.com
STRIPE_KEY=your-stripe-public-key
STRIPE_SECRET=your-stripe-secret-key

publish Cashier's configuration file using the vendor:publish Artisan command:
php artisan vendor:publish --tag="cashier-config"

Create Subscription Controller:
php artisan make:controller SubscriptionController

now we need to handle the user's table to save the stripe payment data:
php artisan make:migration add_subscription_columns_to_users_table --table=users

webhook to handle subscription events to keep your user data in sync with Stripe:
making the controller : php artisan make:controller WebhookController

checking stripe webhook:
Go to the Stripe Dashboard.
Developers > Webhooks
https://docs.stripe.com/stripe-cli
download the compress file and extract it and set it to your environment:
Add a New Endpoint:http://localhost:8000/stripe/webhook

webhooks are coming from an external service, they won't have a CSRF token we have Excluded Webhook Route from CSRF Protection '/stripe/webhook'

note for webhook there is another key :The secret key (sk*test*...) you logged is a Stripe API Secret Key. However, for webhook signature verification, you should be using a webhook secret key, which is different.
define the webhook secret in your local you should find it in the webhook in the stripe dashboard developers section

4. Subscription Restrictions
   Implement middleware to restrict access to premium features for users without an active
   subscription
   Allow users on free trial to access all features
   Redirect users with expired subscriptions to a subscription renewal page

php artisan make:middleware CheckSubscription
