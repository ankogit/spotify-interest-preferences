<?php

use App\Http\Controllers\SpotifyService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
//    session(['accessTokenSpotify' => $accessToken]);

    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('spotify/analyze', [SpotifyService::class, 'analyzeMyProfile'])->name('spotify.analyze')->middleware(['auth', 'spotify.token']);
Route::get('find/friends', [\App\Http\Controllers\PagesController::class, 'friends'])->name('find.friends')->middleware(['auth', 'spotify.token']);


require __DIR__.'/auth.php';
