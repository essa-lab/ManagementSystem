<?php

namespace App\Helper;

use Illuminate\Support\Facades\Log;


class Logger
{
    public static function log($message, $level = 'info')
    {
        if (env('ENABLE_LOG', false)) {
            match ($level) {
                'info' => Log::info($message),
                'warning' => Log::warning($message),
                'error' => Log::error($message),
                'debug' => Log::debug($message),
                'critical' => Log::critical($message),
                default => Log::info($message),
            };
        }
    }
}
