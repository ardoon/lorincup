<?php

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
    $active_menu = 'home';
    return view('welcome', compact('active_menu'));
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('tournaments', 'TournamentController');

Route::post('participants', 'ParticipantController@store')->name('participants.store');

Route::get('tables/{table}', 'TableController@show')->name('tables.show');
Route::get('tables/start/{table}', 'TableController@start')->name('tables.start');
Route::get('tables/end/{table}', 'TableController@end')->name('tables.end');
Route::get('tables/open/{table}', 'TableController@open')->name('tables.open');
Route::get('tables/progress/{table}', 'TableController@progress')->name('tables.progress');
Route::post('tables/win/{table}', 'TableController@win')->name('tables.win');
Route::post('tables/win-double/{table}', 'TableController@winDouble');
Route::get('tables/schema/{tournament}', 'TableController@scheme')->name('tables.scheme');
Route::post('tables', 'TableController@store')->name('tables.store');
