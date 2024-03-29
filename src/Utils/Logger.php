<?php

namespace CloudMyn\Logger\Utils;

use Carbon\Carbon;
use CloudMyn\Logger\Exceptions\LogException;
use Illuminate\Database\Eloquent\Model;

use function CloudMyn\Logger\Helpers\logger_path;

class Logger
{
    /**
     *  Method for get log files
     *
     *  @return array<string>|array<null>
     */
    public function getLogFiles(): array
    {
        $log_path = logger_path();

        if (!file_exists($log_path)) {
            return [];
        }

        $dirs =  array_values(array_diff(scandir($log_path), array('.', '..')));

        return $dirs;
    }

    /**
     *  Method for find a log base on the given id
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @param  bool    $ignore_trace_and_prev
     *
     *  @return array|null
     */
    public function findById(string $file_name, string $value)
    {
        $results  =  $this->whereId($file_name, $value, false);

        if (count($results) === 0 || count($results) >= 2) {
            return null;
        }

        return $results[0];
    }

    /**
     *  Method for find a log data, base on its id
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @param  bool    $ignore_trace_and_prev
     *
     *  @return array
     */
    public function whereId(string $file_name, string $value, bool $ignore_trace_and_prev = false): array
    {
        return $this->find($file_name, 'id', $value, $ignore_trace_and_prev);
    }

    /**
     *  Method for find a log data, base on its class
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @param  bool    $ignore_trace_and_prev
     *
     *  @return array
     */
    public function whereClass(string $file_name, string $value, bool $ignore_trace_and_prev = true): array
    {
        return $this->find($file_name, 'class', $value, $ignore_trace_and_prev);
    }

    /**
     *  Method for find a log data, base on its message
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @param  bool    $ignore_trace_and_prev
     *
     *  @return array
     */
    public function whereMessage(string $file_name, string $value, bool $ignore_trace_and_prev = true): array
    {
        return $this->find($file_name, 'message', $value, $ignore_trace_and_prev);
    }

    /**
     *  Method for find a log data, base on its Ip
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @param  bool    $ignore_trace_and_prev
     *
     *  @return array
     */
    public function whereIp(string $file_name, string $value, bool $ignore_trace_and_prev = true): array
    {
        return $this->find($file_name, 'user_ip', $value, $ignore_trace_and_prev);
    }

    /**
     *  Method for find a log data, base on its user_id
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @param  bool    $ignore_trace_and_prev
     *
     *  @return array
     */
    public function whereUserId(string $file_name, string $value, bool $ignore_trace_and_prev = true): array
    {
        return $this->find($file_name, 'user_id', $value, $ignore_trace_and_prev);
    }

    /**
     *  Method for find a log data, base on a file where an exception is thrown
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @param  bool    $ignore_trace_and_prev
     *
     *  @return array
     */
    public function whereFileName(string $file_name, string $value, bool $ignore_trace_and_prev = true): array
    {
        return $this->find($file_name, 'file_name', $value, $ignore_trace_and_prev);
    }

    /**
     *  Method for get the error logs
     *
     *  @param  string  $file_name
     *  @param  string  $ignore_trace_and_prev
     *
     *  @return array
     */
    public function get(string $file_name, bool $ignore_trace_and_prev = true): array
    {
        $file_path  =   logger_path() . DIRECTORY_SEPARATOR . $file_name;

        if (!file_exists($file_path) || empty($file_name)) {
            return [];
        }

        $byte  = filesize($file_path);

        // jika ukuran file melebihi 800MB than thrown exception
        if (floatval($byte) >= (1048576 * 800)) {
            throw new LogException("The file is to big!");
        }

        $file   =   fopen($file_path, "r");

        $log_data = [];

        if ($file) {
            $index = 0;
            while (!feof($file)) {
                $line = fgets($file);

                switch ($line) {
                    case str_contains($line, "[Log::start]"):
                        $log_data[$index] = [];
                        break;
                    case str_contains($line, "[Log::end]"):
                        $index++;
                        break;
                }

                if (preg_match_all("/^([\w]+:\s+.+;)$/", $line) === 1) {

                    $key    =   explode(":", $line)[0];

                    if ($ignore_trace_and_prev === true) {
                        if ($key === "trace" or $key === "previuos") {
                            continue;
                        }
                    }

                    $value  =   trim(preg_split("/^([\w]+:)/", $line)[1]);

                    // remove semicolon
                    $value  =   rtrim($value, ";\n");

                    $log_data[$index][$key] =   $value;
                }
            }
        }

        fclose($file);

        return array_reverse($log_data);
    }

    /**
     *  Method for log an error into file
     *
     *  @param  \Throwable  $throwable represent Exception|Error
     *  @param  \Illuminate\Database\Eloquent\Model $user the user who caused the error
     *  @return void
     */
    public function log(\Throwable $throwable, ?Model $user = null): void
    {
        if ($throwable instanceof LogException) {
            return;
        }

        try {

            $file_name  =   $this->getFileName();
            $log_path   =   logger_path();

            $path   =   $log_path . DIRECTORY_SEPARATOR . $file_name;

            $user_ip    =   request()->ip() ?? "0.0.0.0";
            $user_id    =   $user instanceof Model ? $user->getKey() : 'null';

            $exception_message  =   str_replace(array("\r", "\n"), ' ', $throwable->getMessage());
            $exception_message  =    preg_replace('/\s+/', ' ', $exception_message);

            if($exception_message === null || $exception_message === "") {
                $exception_message = "-";
            }
            
            $exception_class    =   get_class($throwable);
            $exception_code     =   $throwable->getCode();
            $exception_trace    =   json_encode($throwable->getTrace());
            $exception_previous =   json_encode($throwable->getPrevious());

            $_file_name =   $throwable->getFile();
            $file_line  =   $throwable->getLine();

            $create_at  =   Carbon::now();

            $exception_id = strtoupper(rand(100, 999) . uniqid() . rand(10, 99));

            $content = <<<EOD
            \n
            [Log::start]
            id:         $exception_id;
            class:      $exception_class;
            message:    $exception_message;
            user_ip:    $user_ip;
            user_id:    $user_id;
            code:       $exception_code;
            trace:      $exception_trace;
            previuos:   $exception_previous;
            file_name:  $_file_name;
            file_line:  $file_line;
            create_at:  $create_at;
            [Log::end]
            EOD;

            $permission = "a";

            if (file_exists($path) === false) {
                try {
                    mkdir($log_path);
                } catch (\Throwable $e) {
                }

                $permission =   "w+";
                $content = "\nGeneratedFile DoNot modified this file\n" . $content;
            }

            $file = fopen($path, $permission) or dd("Cannot open log file!");

            fwrite($file, $content);

            fclose($file);

            // ...
        } catch (\Throwable $th) {
            $msg    =   $th->getMessage() .
                " File: " . $th->getFile() .
                " Line: " . $th->getLine();

            throw new LogException($msg, 500, $th);
        }
    }

    public function delete(string $file_name): bool
    {
        $log_path = logger_path();
        $path = $log_path . DIRECTORY_SEPARATOR . $file_name;

        if (!file_exists($path)) {
            return false;
        }

        return unlink($path);
    }

    /**
     *  Method for create a file name
     *
     *  @return string
     */
    public function getFileName(): string
    {
        return date('Y-m-d') . "-logger.log";
    }

    /**
     *  Protected method - for find a specific log in log file
     *
     *  @param  string  $file_name
     *  @param  string  $key
     *  @param  string  $value
     *  @param  bool    $ignore_trace_and_prev determine whether the exception trace should be ignored or not
     *
     *  @return array
     */
    protected function find(string $file_name, string $key, string $value, bool $ignore_trace_and_prev): array
    {
        $logs       =   $this->get($file_name, $ignore_trace_and_prev);
        $matches    =   [];

        array_filter($logs, function (array $data) use ($key, $value, &$matches) {

            if (array_key_exists($key, $data)) {

                // if (strtolower($data[$key]) === strtolower($value)) {
                //     $matches[] = $data;
                // }

                // Create a regular expression pattern with the keyword and wildcards
                $pattern = "/.*" . preg_quote($value, '/') . ".*/i";

                // Use preg_match_all to find matching substrings in the paragraph
                if (preg_match_all($pattern, $data[$key], $_matches)) {
                    foreach ($_matches[0] as $match) {
                        $matches[] = $data;
                    }
                } 

            }

        });

        return $matches;
    }
}
