<?php

namespace CloudMyn\Logger\Utils;

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

        if (!file_exists($log_path)) return [];

        $dirs =  array_values(array_diff(scandir($log_path), array('.', '..')));

        return $dirs;
    }

    /**
     *  Method for find a log data, base on its id
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @return array
     */
    public function whereId(string $file_name, string $value): array
    {
        return $this->find($file_name, 'id', $value);
    }

    /**
     *  Method for find a log data, base on its class
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @return array
     */
    public function whereClass(string $file_name, string $value): array
    {
        return $this->find($file_name, 'class', $value);
    }

    /**
     *  Method for find a log data, base on its message
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @return array
     */
    public function whereMessage(string $file_name, string $value): array
    {
        return $this->find($file_name, 'message', $value);
    }

    /**
     *  Method for find a log data, base on its Ip
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @return array
     */
    public function whereIp(string $file_name, string $value): array
    {
        return $this->find($file_name, 'user_ip', $value);
    }

    /**
     *  Method for find a log data, base on its user_id
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @return array
     */
    public function whereUserId(string $file_name, string $value): array
    {
        return $this->find($file_name, 'user_id', $value);
    }

    /**
     *  Method for find a log data, base on its file_name
     *
     *  @param  string  $file_name
     *  @param  string  $value
     *  @return array
     */
    public function whereFileName(string $file_name, string $value): array
    {
        return $this->find($file_name, 'file_name', $value);
    }

    /**
     *  Method for get the error logs
     *
     *  @param  string  $file_name
     *  @return array
     */
    public function get(string $file_name): array
    {
        $file_path  =   logger_path() . DIRECTORY_SEPARATOR . $file_name;

        if (!file_exists($file_path) || empty($file_name)) return [];

        $byte  = filesize($file_path);

        // jika ukuran file melebihi 800MB than thrown exception
        if (floatval($byte) >= (1048576 * 800))
            throw new LogException("The file is to big!");

        $file   =   fopen($file_path, "r");

        $log_data = [];

        if ($file) {
            $index = 0;
            while (!feof($file)) {
                $line = fgets($file);

                switch ($line) {
                    case str_contains($line, "[Exception:Start]"):
                        $log_data[$index] = [];
                        break;
                    case str_contains($line, "[Exception:End]"):
                        $index++;
                        break;
                }

                if (preg_match_all("/^([\w]+:\s+.+;)$/", $line) === 1) {
                    $key    =   explode(":", $line)[0];
                    $value  =   trim(preg_split("/^([\w]+:)/", $line)[1]);

                    // remove semicolon
                    $value  =   rtrim($value, ";\n");

                    $log_data[$index][$key] =   $value;
                }
            }
        }

        fclose($file);

        return $log_data;
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
        if ($throwable instanceof LogException) return;

        try {

            $file_name  =   $this->getFileName();
            $log_path   =   logger_path();

            $path   =   $log_path . DIRECTORY_SEPARATOR . $file_name;

            $user_ip    =   request()->ip() ?? "0.0.0.0";
            $user_id    =   $user instanceof Model ? $user->getKey() : 'null';

            $exception_class    =   get_class($throwable);
            $exception_message  =   $throwable->getMessage();
            $exception_code     =   $throwable->getCode();
            $exception_trace    =   json_encode($throwable->getTrace());
            $exception_previous =   json_encode($throwable->getPrevious());

            $_file_name =   $throwable->getFile();
            $file_line  =   $throwable->getLine();

            $create_at  =   time();

            $str = base64_encode($exception_class);
            $str = str_replace("=", '1', $str);

            $exception_id = uniqid("$str.");

            $content = <<<EOD
            \n
            [Exception:Start]
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
            [Exception:End]
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

        if (!file_exists($path)) return false;

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
     *  @return array
     */
    protected function find(string $file_name, string $key, string $value): array
    {
        $logs       =   $this->get($file_name);
        $matches    =   [];

        array_filter($logs, function (array $data) use ($key, $value, &$matches) {
            if (array_key_exists($key, $data)) {
                if ($data[$key] === $value) {
                    $matches[] = $data;
                }
            }
        });

        return $matches;
    }
}
