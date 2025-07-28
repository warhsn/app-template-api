<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(
        'https://app.thriveutilities.co'
    );
})->name('dashboard');