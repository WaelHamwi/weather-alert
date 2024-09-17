<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\CheckSubscription;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeatherUpdateMail;
use App\Jobs\CheckWeatherForUsers;
use App\Services\WeatherService;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Notifications\TrialExpirationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $notifications = collect();

    if (Auth::check()) {
        $user = Auth::user();
        $notifications = $user->notifications->whereNull('read_at');
    }

    return view('welcome', compact('notifications'));
});

Route::middleware('auth')->group(function () {
    Route::get('/test-notification', function () {
        $user = User::find(1); // Replace with a valid user ID
        $user->notify(new TrialExpirationNotification(
            'Your trial is about to expire soon. Please renew your subscription.',
            url('/subscribe/renew')
        ));

        return 'Notification sent!';
    });
    // Testing the job queue

    Route::get('/test-weather-job', function () {

        // Create a test user and weather data
        $user = User::first(); // Retrieve the first user for testing


        try {
            Log::info(`{'Attempting to send weather email to: ' . $user->email}`);
            //   $mailo = Mail::to("haw6218@gmail.com")->send(new WeatherUpdateMail($user, $weatherData));
            dispatch(new CheckWeatherForUsers(app(WeatherService::class)));

            //weatherupdate mail is for the sending email template
            //checkweathersforusers is confirming the subscribed users  and WeatherService is having the methods to print the weather data
            Log::info('Weather email sent!');
            return 'Weather email sent!';
        } catch (\Exception $e) {
            Log::error('Mail error: ' . $e->getMessage());
            return 'Failed to send email.';
        }
        // return 'Weather job dispatched!';
    });
    //protected routes to Explore Weather Features
    Route::middleware(CheckSubscription::class)->group(function () {
        Route::get('/weather/current', [WeatherController::class, 'showCurrentWeather'])->name('weather.current');
        Route::get('/weather/forecast', [WeatherController::class, 'showHourlyForecast'])->name('weather.forecast');

        //notification parts 
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    });

    //hanlde the subscribiton process 

    Route::get('/not-subscribed', [SubscriptionController::class, 'index'])->name('subscribe.not-subscribed');
    Route::post('/check-trial', [SubscriptionController::class, 'checkTrial'])->name('subscribe.checkTrial');
    Route::post('/checkout', [SubscriptionController::class, 'checkout'])->name('subscribe.checkout');
    Route::get('/success', [SubscriptionController::class, 'success'])->name('subscribe.success');
    Route::get('/cancel', [SubscriptionController::class, 'cancel'])->name('subscribe.cancel');
    Route::post('/cancel-subscription', [SubscriptionController::class, 'cancelSubscription'])->name('subscribe.cancelSubscription');
    Route::get('/customer-portal', [SubscriptionController::class, 'customerPortal'])->name('customer.portal');
    Route::post('/subscribe/renew', [SubscriptionController::class, 'renewSubscription'])->name('subscribe.renew');
});
Route::post('/stripe/webhook', [WebhookController::class, 'handle']);

//hanlde the log in process 
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
