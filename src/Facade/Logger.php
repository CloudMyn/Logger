<?php

namespace CloudMyn\Logger\Facade;

use Illuminate\Support\Facades\Facade;

/**
 *  @method static void log(\Throwable $throwable, ?\Illuminate\Database\Eloquent\Model $user)
 *  @method static array get(string $file_name) Method for get the error logs
 *  @method static array<string>|array<null> getLogFiles() Method for get log files
 *  @method static array whereId(string $file_name, string $value, bool $ignore_trace_and_prev) Method for find a log data, base on its id
 *  @method static array whereClass(string $file_name, string $value, bool $ignore_trace_and_prev) Method for find a log data, base on its class
 *  @method static array whereMessage(string $file_name, string $value, bool $ignore_trace_and_prev) Method for find a log data, base on its message
 *  @method static array whereIp(string $file_name, string $value, bool $ignore_trace_and_prev) Method for find a log data, base on its Ip
 *  @method static array whereUserId(string $file_name, string $value, bool $ignore_trace_and_prev) Method for find a log data, base on its user_id
 *  @method static array whereFileName(string $file_name, string $value, bool $ignore_trace_and_prev) Method for find a log data, base on a file where an exception is thrown
 */
class Logger extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'logger';
    }
}
