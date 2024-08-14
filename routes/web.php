<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\RecommenderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::middleware(['auth', 'check.spotify.token', 'password.set'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/recommender', [RecommenderController::class, 'index'])->name('recommender');
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/playlists', [PlaylistController::class, 'index'])->name('playlists.index');
    Route::get('/playlists/{playlist}', [PlaylistController::class, 'show'])->name('playlists.show');
    Route::get('/getSpotifyResults', [SpotifyController::class, 'search'])->name('get.spotify.results');
    Route::get('/getRecommendations', [SpotifyController::class, 'recommendations'])->name('get.recommendations');
    Route::get('/profile', [UserController::class, 'show'])->name('profile.show');
});

Route::get('/', [AuthController::class, 'index'])->name('welcome');
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/password-setup', [AuthController::class, 'setupPassword'])->name('password.setup');
Route::post('/password-setup', [AuthController::class, 'storePassword'])->name('password.store');

Route::get('/spotify/login', [SpotifyController::class, 'login'])->name('spotify.login');
Route::get('/spotify/callback', [SpotifyController::class, 'callback'])->name('spotify.authorize');
Route::post('/spotify/createPlaylist', [SpotifyController::class, 'createPlaylist'])->name('spotify.createPlaylist');
