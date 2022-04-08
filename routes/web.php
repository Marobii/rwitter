<?php
/* namespace App\Http\Controllers\Auth; */
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
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
Auth::routes();

Route::get('/', function () {
    return view('welcome');
});


Route::get('/rwitterhome', function () {
    return view('rwitterhome');
});


Route::get('/rwitterhome', 'RwitterController@index')->middleware('auth');
Route::post('/rwit', 'RwitterController@store');
Route::post('/funfa/{id}', 'RwitterController@funfat');
Route::post('/godown/{id}', 'RwitterController@funfadown');
Route::delete('/rwitterhome/{id}', 'RwitterController@destroy')->middleware('auth');


Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
