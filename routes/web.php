<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/dashboard', function () {
    return redirect('/admin');
});

Route::get('/admin/aura-dashboard', function () {
    return redirect('/admin');
});
