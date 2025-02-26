<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');  
});

Route::post('/fetch', [ApiController::class, 'fetchData']);