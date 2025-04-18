<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="1.0.0"
 * )
 * @OA\Components(
 *     @OA\Schema(
 *         schema="Player",
 *         type="object",
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             description="ID игрока"
 *         ),
 *         @OA\Property(
 *             property="username",
 *             type="string",
 *             description="Имя игрока"
 *         ),
 *         @OA\Property(
 *             property="avatar_id",
 *             type="integer",
 *             description="Avatar игрока"
 *         ),
 *         @OA\Property(
 *             property="hp",
 *             type="integer",
 *             description="HP игрока"
 *         ),
 *         @OA\Property(
 *             property="money",
 *             type="integer",
 *             description="Money игрока"
 *         ),
 *     ),
 *     @OA\Schema(
 *         schema="FriendRequest",
 *         type="object",
 *         @OA\Property(
 *             property="player_id",
 *             type="integer",
 *             description="ID игрока, который отправил заявку"
 *         ),
 *         @OA\Property(
 *             property="friend_id",
 *             type="integer",
 *             description="ID игрока, которому отправлена заявка"
 *         ),
 *         @OA\Property(
 *             property="status",
 *             type="string",
 *             description="Статус заявки (pending, accepted)"
 *         ),
 *     ),
 *     @OA\Schema(
 *         schema="Score",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=5),
 *         @OA\Property(property="player_id", type="integer", example=1),
 *         @OA\Property(property="level_id", type="integer", example=2),
 *         @OA\Property(property="score", type="integer", example=1500),
 *         @OA\Property(property="count_of_stars", type="integer", example=3),
 *         @OA\Property(property="completed", type="integer", example=1),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-17T12:34:56Z"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-17T12:34:56Z")
 *        )
 *   )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Локальный сервер разработки"
 * )
 */
class SwaggerController extends Controller
{
    // Пустой контроллер, только для аннотаций
}