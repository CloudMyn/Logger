# Logger - Laravel Package For Handle Error logging

This package allow you to manage your error log in a simple way

## Screenshot

<img src="screens/main.jpeg" width="100%"/>

## Instalation

You may copy the code below, and paste into your project terminal, we are assuming your machine have a composer installed 

    composer require cloudmyn/logger
  
## Usage

Fisrt you have to publish the config file:

    php artisan vendor:publish --provider="CloudMyn\Logger\LoggerServiceProvider"

Goto the path **App\Exceptions\Handler.php** and add this code **Logger::log($throwable, auth()->user());** inside the report method

```PHP
<?php

namespace App\Exceptions;

use CloudMyn\Logger\Facade\Logger;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{

  // ...

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $throwable
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $throwable)
    {
        Logger::log($throwable, auth()->user());
        
        parent::report($throwable); // you can removed this if you want
    }
  
  // ...

}

```

Now you can visit this url **http://locahost:8000/logger/show** where the logs is listed
