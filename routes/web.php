 <?php

use Illuminate\Support\Facades\Route;
Route::get('/hello', function () {
    return 'Hello, Devlet!';
});
Route::get('/', function () {
    return view('welcome');
});
