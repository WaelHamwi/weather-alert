<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WebhookController;
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
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/weather/current', [WeatherController::class, 'showCurrentWeather'])->name('weather.current');
    Route::get('/weather/forecast', [WeatherController::class, 'showHourlyForecast'])->name('weather.forecast');
    //hanlde the subscribiton process 
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
