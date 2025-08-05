<?php

use App\Http\Middleware\RolePermissionWithAlertMiddleware;
use Illuminate\Foundation\Application;
use App\Http\Middleware\TwoFactorMiddleware;
use App\Http\Middleware\LogUserDeviceMiddleware;
use App\Http\Middleware\RolePermissionMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom middleware
        $middleware->alias([
            'role.permission' => RolePermissionMiddleware::class,
            'role.permission.alert' => RolePermissionWithAlertMiddleware::class,
            '2fa' => TwoFactorMiddleware::class,
            'log_user_device' => LogUserDeviceMiddleware::class,
        ]);


        //HOW TO USE
        /**
         * Route::get('/users', [UserController::class, 'index'])
                ->middleware('role.permission:role:DSI God Admin,permission:user list view')
                ->name('user.index');
         * 
         * or 
         * 
         * Route::get('/users', [UserController::class, 'index'])
                ->middleware('role.permission:role:DSI God Admin,permission:user list view')
                ->name('user.index');

         * 
         */



    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
