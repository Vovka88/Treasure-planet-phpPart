<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Глобальные middleware, которые выполняются для каждого запроса.
     */
    // protected $middleware = [
        // \App\Http\Middleware\Cors::class, // Добавляем поддержку CORS
    // ];

    /**
     * Middleware групп.
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    
        'api' => [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
}
