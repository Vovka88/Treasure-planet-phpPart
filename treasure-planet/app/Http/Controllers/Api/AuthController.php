<?php

/**
 * @SuppressWarnings("App\Http\Middleware\Cors")
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Token;
use App\Models\Scores;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Авторизация пользователя",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Успешная авторизация",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="player_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неверные данные"
     *     )
     * )
     */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $player = Player::where('email', $request->email)->first();

        if (!$player || $player->password !== $request->password) {
            return response()->json(['error' => 'Неверные данные'], 401);
        }

        $token = Str::random(60);

        Token::updateOrCreate(
            ['player_id' => $player->id],
            ['token' => $token, 'expires_at' => now()->addDays(30)]
        );


        return response()->json(['token' => $token, 'player_id' => $player->id], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Выход пользователя",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string", example="abcdef123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Выход выполнен"
     *     )
     * )
     */

    public function logout(Request $request)
    {

        $request->validate([
            'token' => 'required|string',
        ]);

        $token = Token::where('token', $request->token)->delete();
        // Player::where('id', $token->player_id)->delete();
    }
    /**
     * @OA\Post(
     *     path="/api/registration",
     *     summary="Регистрация нового пользователя",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="newuser@example.com"),
     *             @OA\Property(property="password", type="string", example="securepassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Аккаунт создан",
     *         @OA\JsonContent(
     *             @OA\Property(property="result", type="string", example="Аккаунт создан")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Не получилось создать аккаунт"
     *     )
     * )
     */

    public function registration(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $playerIsReg = Player::where('email', $request->email)->first();

        if (empty($playerIsReg)) {
            $player = Player::create([
                'username' => "no_data",
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $scores = Scores::create([
                'player_id' => $player->id,
            ]);

            return response()->json(['result' => 'Аккаунт создан'], 201);
        }

        return response()->json(['error' => 'Не получилось создать аккаунт'], 401);
    }

    /**
     * @OA\Post(
     *     path="/api/deleteaccount",
     *     summary="Удаление пользователя",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id"},
     *             @OA\Property(property="player_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Аккаунт успешно удалён"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Не получилось удалить аккаунт"
     *     )
     * )
     */
    public function deleteUser(Request $request)
    {

        $request->validate([
            'player_id' => 'required|integer',
        ]);

        $player = Player::where('id', $request->player_id)->first();

        if (!empty($player)) {
            $player->delete();

            return response()->json(['result' => 'Аккаунт успешно удалён'], 201);
        }

        return response()->json(['error' => 'Не получилось удалить аккаунт'], 401);
    }
    /**
     * @OA\Post(
     *     path="/api/loginByToken",
     *     summary="Проверка токена и получение данных игрока",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string", example="abcdef123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Токен действителен",
     *         @OA\JsonContent(
     *             @OA\Property(property="result", type="string", example="Всё ок"),
     *             @OA\Property(property="player_id", type="integer"),
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="player_avatar_id", type="integer"),
     *             @OA\Property(property="player_hp", type="integer"),
     *             @OA\Property(property="player_money", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Произошла ошибка, не удалось вернуть айди аккаунта"
     *     )
     * )
     */

    public function getToken(Request $request)
    {

        $request->validate([
            'token' => 'required|string'
        ]);

        $token = Token::where('token', $request->token)->first();
        $player = Player::where('id', $token->player_id)->first();


        if (!empty($token)) {
            return response()->json(
                [
                    'result' => 'Всё ок',
                    'player_id' => $token->player_id,
                    'username' => $player->username,
                    'player_avatar_id' => $player->avatar_id,
                    'player_hp' => $player->hp,
                    'player_money' => $player->money
                ],
                201
            );
        } else {
            return response()->json(['error' => 'Произошла ошибка, не удалось вернуть айди аккаунта'], 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/updateusername",
     *     summary="Обновить имя пользователя и аватар",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id", "username", "avatar_id"},
     *             @OA\Property(property="player_id", type="integer", example=1),
     *             @OA\Property(property="username", type="string", example="НовоеИмя"),
     *             @OA\Property(property="avatar_id", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Всё ок"
     *     )
     * )
     */
    public function updateUsernameAndAvatar(Request $request)
    {

        $request->validate([
            'player_id' => 'required|integer',
            'username' => 'required|string',
            'avatar_id' => 'required|integer'
        ]);

        Player::where(['id' => $request->player_id])->update(['username' => $request->username, 'avatar_id' => $request->avatar_id]);

        return response()->json(['result' => 'Всё ок'], 201);
    }
}
