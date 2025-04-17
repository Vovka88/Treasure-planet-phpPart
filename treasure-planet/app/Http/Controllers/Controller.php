<?php

namespace App\Http\Controllers;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Treasure Planet API",
 *     version="1.0.0",
 *     description="API для авторизации, регистрации и управления игроками",
 *     @OA\Contact(
 *         email="you@example.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Локальный сервер разработки"
 * )
 */

abstract class Controller
{
    //
}
