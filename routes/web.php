<?php

use CloudMyn\Logger\Facade\Logger;
use Illuminate\Support\Facades\Route;

Route::get('/show/{filename?}', '\CloudMyn\Logger\Http\Controllers\LoggerController@show')->name('logger.show');

Route::get('/ajax/trace/{filename}/{id}', '\CloudMyn\Logger\Http\Controllers\LoggerController@ajaxTrace')->name('ajax.trace');

Route::get('/{filename}/delete', '\CloudMyn\Logger\Http\Controllers\LoggerController@delete')->name('logger.delete');
