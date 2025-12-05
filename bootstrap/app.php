<?php

use App\Http\Middleware\AcceptJsonMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Application;
use Modules\Law\Console\Commands\BillingCommand;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(AcceptJsonMiddleware::class);
        $middleware->append(\App\Http\Middleware\LanguageMiddleware::class);
        $middleware->append(\App\Http\Middleware\DetectUserAgentMiddleware::class);
        $middleware->validateCsrfTokens(except: [
            'whatsapp/webhook*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // $exceptions->map(\app\Exceptions\Handler::class);
    })
    ->withCommands([
            BillingCommand::class,
    ])
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command(BillingCommand::class)->dailyAt('04:00')->timezone('Asia/Riyadh');
      
    })
    ->create();
