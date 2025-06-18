<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(
            [
                'admin' => AdminMiddleware::class,
            ]
        );
        // $middleware->redirectGuestsTo(function (Request $request) {
        //     if ($request->routeIs('admin.*')) {
        //         return route('admin.attendanceList');
        //     }
        //     return route('login');
        // });
        // $middleware->redirectUsersTo(function (Request $request) {
        //     return match (true) {
        //         $request->is('admin/*')    => route('admin.attendanceList'),
        //     };
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
