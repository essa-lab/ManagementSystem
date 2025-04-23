<?php

use App\Models\Resource\LibrarySetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: [__DIR__ . '/../routes/api.php'],
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: []);

        $middleware->alias([
            'jwt' => App\Http\Middleware\JwtMiddleware::class,
            'jwt.patron' => App\Http\Middleware\JwtMiddleware::class . ':patron',
            'jwt.user' => App\Http\Middleware\JwtMiddleware::class . ':user',
            'localization' => App\Http\Middleware\LocalizationMiddleware::class,
            'role' => App\Http\Middleware\RoleMiddleware::class,
            'verified' => App\Http\Middleware\VerifiedMiddleware::class,
            'selfRegisteration' => App\Http\Middleware\SelfRegisterationMiddleware::class,


        ]);

        //
    })->withSchedule(function (Schedule $schedule) {
        $schedule->command('notify:due-overdue')->dailyAt(LibrarySetting::value('scheduler_time') ?? '09:00');
        $schedule->command('penalty:calculate')->dailyAt('03:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
