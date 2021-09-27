<?php

use Illuminate\Support\Facades\Route;
use League\Flysystem\FileNotFoundException;

Route::get('/show/{filename?}', '\CloudMyn\Logger\Http\Controllers\LoggerController@show')->name('logger.show');

Route::get('/{filename}/delete', '\CloudMyn\Logger\Http\Controllers\LoggerController@delete')->name('logger.delete');

Route::get('/throw/error', function () {

    for ($i = 0; $i <= 100; $i++) {
        try {
            $unique = uniqid();

            throw new FileNotFoundException("x:\\test\\$unique.php");
        } catch (\Throwable $th) {
            report($th);
        }
    }

    dd("SELESAI");
});
