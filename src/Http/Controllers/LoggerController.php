<?php

namespace CloudMyn\Logger\Http\Controllers;

use CloudMyn\Logger\Facade\Logger;

class LoggerController  extends Controller
{
    public function show(?string $filename = null)
    {
        $log_files  =   Logger::getLogFiles();

        if (is_null($filename)) $filename = last($log_files);

        $log_data   =   Logger::get($filename);

        if (empty($log_files)) $filename = null;

        try {
            return view('cloudmyn_logger::logger-show', [
                'files' =>  $log_files,
                'logs'  =>  $log_data,
                'c_file'  =>  $filename,
            ]);
        } catch (\Throwable $th) {
            return "Error: " . $th->getMessage();
        }
    }

    public function delete(string $filename)
    {
        Logger::delete($filename);
        return redirect()->route('logger.show');
    }
}
