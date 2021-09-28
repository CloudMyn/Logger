<?php

use Illuminate\Support\Facades\Route;

Route::get('/show/{filename?}', '\CloudMyn\Logger\Http\Controllers\LoggerController@show')->name('logger.show');

Route::get('/{filename}/delete', '\CloudMyn\Logger\Http\Controllers\LoggerController@delete')->name('logger.delete');
