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



5. Queue Implementation
Use Laravel queues for:
Scheduling periodic weather checks (every hour) for all active user locations
Generating and sending weather alert notifications
Processing subscription-related tasks (e.g., sending trial expiration reminders)

1-
use the database driver.
In .env:
QUEUE_CONNECTION=database

Create the jobs table:
php artisan queue:table
php artisan migrate

we need to run the job queue:php artisan queue:work

creating the job:
php artisan make:job CheckWeatherForUsers

and inside the kernel The weather check can be scheduled in the App\Console\Kernel.php file.
so now we write the logic in the kernel


2-
 Generate Mailable Class:
 php artisan make:mail WeatherUpdateMail

Ensure that your .env file is configured with the correct mail settings. For example, using SMTP:


MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yourname@gmail.com
MAIL_PASSWORD=yourpassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=yourname@gmail.com
MAIL_FROM_NAME="${APP_NAME}"


go to your manage acount to get the mail user name and password-> security-> serach for app passwords
if your 2-step verification is not activated please activate it to get the password of the app 
Find 2-Step Verification:

or Under the "Signing in to Google" section, you should see 2-Step Verification. Click on it.
app paswords :name you app like: weather-alert-app
and copy your password 


for the queue sometimes is hanging :php artisan queue:restart
 php artisan queue:work   

 or we can test it through the kernel console:
 php artisan schedule:run



3-
generate a new job class for handling trial expiration reminders
php artisan make:job TrialExpirationReminder

 Create the Notification
 php artisan make:notification TrialExpirationNotification


php artisan notifications:table
php artisan migrate

now we make the controller :php artisan make:controller NotificationController

In Laravel, notifications are automatically associated with users (or other notifiable entities) using the Notifiable trait



6. Notification System
Implement a notification system to alert users about:
Rain forecasted in the next hour for their locations
Severe weather alerts
Subscription status (e.g., trial expiration, renewal reminders)
Use Laravel's notification system with multiple channels (email, database)

let us create the actual notification:
php artisan make:notification RainForecastNotification

first let us create the forecast check and notify users:
php artisan make:command CheckRainForecast
for testing the case of the forecast raining :php artisan weather:check-rain    

create a weather sever notification mail:SevereWeatherAlertNotification
