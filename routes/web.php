<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::middleware('api')
            ->prefix('api') 
            ->group(base_path('routes/api.php'));