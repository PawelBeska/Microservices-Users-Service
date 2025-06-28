<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\TokenController;
use App\Http\Requests\Api\v1\ExternalRelationController;
use Illuminate\Support\Facades\Route;

Route::get('/token.json', [TokenController::class, 'token']);

Route::post('external-relations/{table}/batch', ExternalRelationController::class)
    ->name('external-relations')
    ->where('table', '[a-z]+');

Route::controller(AuthController::class)
    ->name('auth.')
    ->prefix('auth')
    ->group(function () {
        Route::post('register', 'register')->name('register');
        Route::post('login', 'login')->name('login');

        Route::get('external/{resolver}', 'externalAuthAccountLogin')->name('external.login');
        Route::get('external/{resolver}/callback', 'externalAuthAccountCallback')->name('external.callback');

        Route::prefix('password')
            ->name('password.')
            ->group(function () {
                Route::post('forgot', 'forgotPassword')->name('forgot');
                Route::post('reset', 'resetPassword')->name('reset');
                Route::post('change', 'changePassword')->name('change')->middleware('auth');
            });
    });
