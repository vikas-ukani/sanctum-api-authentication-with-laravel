<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

$publicPostAuthControllerRoutes = ['register', 'login'];
foreach ($publicPostAuthControllerRoutes as $route) {
    Route::post('/' . $route, [AuthController::class, $route])->name($route);
}

Route::group(['middleware' => ['auth:sanctum']], function () {
    $privateGetAuthControllerRoutes = ['me', 'refresh'];
    foreach ($privateGetAuthControllerRoutes as $route) {
        Route::get('/' . $route, [AuthController::class, $route])->name($route);
    }
});
