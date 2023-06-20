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

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth::routes();
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
Route::get('/dms', function () {
    return view('dms');
});
Route::get('/approved', 'HomeController@dms_approved')->middleware('GSBH:GSBH');
Route::get('/submission-register', 'HomeController@submission_register');
Route::get('/program-admin', 'HomeController@program_admin')->middleware('role:QLGS');
Route::get('/program', 'HomeController@program')->middleware('role:QLGS');
Route::get('/suppervision', 'HomeController@suppervision')->middleware('role:QLGS');
Route::get('/program-stages', 'HomeController@program_stages')->middleware('role:QLGS');


// Route::get('/approved', function () {
//     return view('dms_approved');
// });
// Route::get('/submission-register', function () {
//     return view('dms_register');

// });
Route::get('/customer', 'HomeController@customer');
Route::get('/lookup-customer', 'HomeController@lookup_customer');

