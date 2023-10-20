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

        return view('cloudmyn_logger::logger-show', [
            'files' =>  $log_files,
            'logs'  =>  $log_data,
            'c_file'  =>  $filename,
        ]);
    }

    public function ajaxTrace(string $filename, string $id)
    {
        $log = Logger::whereId($filename, $id);

        $view = config('logger.trace_view', 'cloudmyn_logger::components.stack-trace');

        if (count($log) === 0) return "No-Data";

        try {
            $traces = json_decode($log[0]['trace']);
        } catch (\Throwable $th) {
            $traces = [];
        }

        return view($view, [
            'traces'   =>  $traces,
        ]);
    }

    public function delete(?string $filename = null)
    {
        if (is_null($filename)) return redirect()->back();

        Logger::delete($filename);

        return redirect()->route('logger.show');
    }
}
