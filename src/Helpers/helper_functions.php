<?php

namespace CloudMyn\Logger\Helpers;


/**
 *  Function to get the path to the log file
 */
if (!function_exists('CloudMyn\Logger\Helpers\logger_path')) {

    function logger_path(): string
    {
        $path = ["logs", "logger"];
        return  storage_path(join(DIRECTORY_SEPARATOR, $path));
    }
}


if (!function_exists('CloudMyn\Logger\Helpers\convert_byte')) {
    /**
     * Converts bytes into human readable file size.
     *
     * @param string $bytes
     * @return string human readable file size (2,87 Мб)
     */
    function convert_byte($bytes): string
    {
        $bytes = floatval($bytes);
        $arBytes = [
            0 => [
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ],
            1 => [
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ],
            2 => [
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ],
            3 => [
                "UNIT" => "KB",
                "VALUE" => 1024
            ],
            4 => [
                "UNIT" => "B",
                "VALUE" => 1
            ],
        ];

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", ",", strval(round($result, 2))) . " " . $arItem["UNIT"];
                break;
            }
        }
        return $result;
    }
}

if(! function_exists('CloudMyn\Logger\Helpers\str_limit')) {
    function str_limit(string $str, int $limit, $dots = "...") {
        if(strlen($str) >= $limit) $str = substr($str, 0, $limit) . $dots;
        return $str;
    }
}
