<?php

/**
 * @SuppressWarnings("App\Http\Middleware\Cors")
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Friends;
use App\Models\Player;
use OpenApi\Annotations as OA;

class FriendsController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/getfriends",
     *     summary="Получить список друзей пользователя",
     *     description="Возвращает список друзей игрока со статусом 'accepted'.",
     *     tags={"Friends"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id"},
     *             @OA\Property(property="player_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Список друзей",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="players",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Player")
     *             )
     *         )
     *     )
     * )
     */
    public function getFriends(Request $request)
    {
        $request->validate([
            'player_id' => 'required|numeric'
        ]);

        // Друзья, которым игрок сам отправил и они приняли
        $friends_a = Friends::where([
            'player_id' => $request->player_id,
            'status' => 'accepted'
        ])->get();

        // Друзья, которые отправили игроку заявку и он принял
        $friends_b = Friends::where([
            'friend_id' => $request->player_id,
            'status' => 'accepted'
        ])->get();

        $friendIds = [];

        foreach ($friends_a as $friend) {
            $friendIds[] = $friend->friend_id;
        }

        foreach ($friends_b as $friend) {
            $friendIds[] = $friend->player_id;
        }

        // Получаем всех друзей одним запросом
        $players = Player::whereIn('id', $friendIds)->get();

        return response()->json(['players' => $players], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/getplayers",
     *     summary="Получить список пользователей",
     *     description="Возвращает список игроков.",
     *     tags={"Friends"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id"},
     *             @OA\Property(property="player_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Список игроков",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="players",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Player")
     *             )
     *         )
     *     )
     * )
     */

    public function getPlayers(Request $request)
    {
        $request->validate([
            'player_id' => 'required|numeric'
        ]);

        $currentPlayerId = $request->player_id;

        // Получаем ID всех друзей (в обе стороны, если нужно)
        $friendIds = Friends::where('player_id', $currentPlayerId)
            ->orWhere('friend_id', $currentPlayerId)
            ->pluck('player_id')
            ->merge(
                Friends::where('friend_id', $currentPlayerId)->pluck('friend_id')
            )
            ->unique()
            ->toArray();

        // Добавляем самого себя в исключения
        $friendIds[] = $currentPlayerId;

        Friends::where(function ($query) use ($currentPlayerId) {
            $query->where('player_id', $currentPlayerId)
                ->orWhere('friend_id', $currentPlayerId);
        })->where('status', 'accepted');

        // Получаем всех игроков, которые не в списке друзей и не сам игрок
        $players = Player::whereNotIn('id', $friendIds)->get();

        return response()->json(['players' => $players], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/getfriendsinvites",
     *     summary="Получить список входящих заявок в друзья",
     *     description="Возвращает список заявок со статусом 'pending'.",
     *     tags={"Friends"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id"},
     *             @OA\Property(property="player_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Список заявок",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="friends",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/FriendRequest")
     *             )
     *         )
     *     )
     * )
     */


    public function getInvitesToFriends(Request $request)
    {
        $request->validate([
            'player_id' => 'required|numeric',
        ]);

        // Получаем ID игроков, отправивших запрос текущему игроку
        $inviterIds = Friends::where([
            'friend_id' => $request->player_id,
            'status' => 'pending',
        ])->pluck('player_id');

        // Получаем сами модели игроков
        $players = Player::whereIn('id', $inviterIds)->get();

        return response()->json(['players' => $players], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/sendfriendinvite",
     *     summary="Отправить заявку в друзья",
     *     description="Создаёт заявку в друзья от одного игрока к другому.",
     *     tags={"Friends"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id", "friend_id"},
     *             @OA\Property(property="player_id", type="integer", example=1),
     *             @OA\Property(property="friend_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Заявка отправлена",
     *         @OA\JsonContent(
     *             @OA\Property(property="result", type="string", example="Заявка отправлена")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Ошибка отправки",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Ошибка отправки")
     *         )
     *     )
     * )
     */

    public function sendFriendInvite(Request $request)
    {
        $request->validate([
            'player_id' => 'required|numeric',
            'friend_id' => 'required|numeric'
        ]);


        $friends = Friends::where(['player_id' => $request->player_id, 'friend_id' => $request->friend_id])->get();

        if ($friends->isNotEmpty()) {
            return response()->json(['message' => 'Заявка уже отправлена'], 200);
        }

        $friend = null;
        if ($friends->isEmpty()) {
            $friend = Friends::create([
                'player_id' => $request->player_id,
                'friend_id' => $request->friend_id,
                'status' => 'pending'
            ]);
        }

        if (!empty($friend)) {
            return response()->json(['result' => 'Заявка отправлена'], 201);
        } else {
            return response()->json(['error' => 'Ошибка отправки'], 401);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/acceptfriendinvite",
     *     summary="Принять заявку в друзья",
     *     description="Принимает входящую заявку и устанавливает статус 'accepted'.",
     *     tags={"Friends"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id", "friend_id"},
     *             @OA\Property(property="player_id", type="integer", example=1),
     *             @OA\Property(property="friend_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Друг добавлен",
     *         @OA\JsonContent(
     *             @OA\Property(property="result", type="string", example="Друг добавлен")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Ошибка добавления",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Ошибка добавления")
     *         )
     *     )
     * )
     */

    public function acceptFriendInvite(Request $request)
    {
        $request->validate([
            'player_id' => 'required|numeric',
            'friend_id' => 'required|numeric'
        ]);

        $friend = Friends::where(['player_id' => $request->player_id, 'friend_id' => $request->friend_id,  'status' => 'pending'])->first();
        $friend->update(['status' => 'accepted']);

        if (!empty($friend)) {
            return response()->json(['result' => 'Друг добавлен'], 201);
        } else {
            return response()->json(['error' => 'Ошибка добавления'], 401);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/declinefriendinvite",
     *     summary="Отклонить заявку в друзья",
     *     description="отклоняет входящую заявку и удаляет её.",
     *     tags={"Friends"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id", "friend_id"},
     *             @OA\Property(property="player_id", type="integer", example=1),
     *             @OA\Property(property="friend_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Заявка удалена",
     *         @OA\JsonContent(
     *             @OA\Property(property="result", type="string", example="Друг добавлен")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Ошибка удаления",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Ошибка добавления")
     *         )
     *     )
     * )
     */

     public function declineFriendInvite(Request $request)
     {
         $request->validate([
             'player_id' => 'required|numeric',
             'friend_id' => 'required|numeric'
         ]);
     
         $friend = Friends::where(function ($query) use ($request) {
             $query->where('player_id', $request->player_id)
                   ->where('friend_id', $request->friend_id);
         })->orWhere(function ($query) use ($request) {
             $query->where('player_id', $request->friend_id)
                   ->where('friend_id', $request->player_id);
         })->where('status', 'pending')->first();
     
         if ($friend) {
             $friend->delete();
             return response()->json(['result' => 'Заявка отклонена'], 200);
         }
     
         return response()->json(['error' => 'Заявка не найдена'], 404);
     }
     
    /**
     * @OA\Post(
     *     path="/api/deletefriend",
     *     summary="Удалить друга",
     *     description="Удаляет друга из списка друзей.",
     *     tags={"Friends"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id", "friend_id"},
     *             @OA\Property(property="player_id", type="integer", example=1),
     *             @OA\Property(property="friend_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Произошло удаление",
     *         @OA\JsonContent(
     *             @OA\Property(property="result", type="string", example="Произошло удаление")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Ошибка удаления",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Ошибка удаления")
     *         )
     *     )
     * )
     */

    public function deleteFriend(Request $request)
    {
        $request->validate([
            'player_id' => 'required|numeric',
            'friend_id' => 'required|numeric'
        ]);

        $friend = Friends::where(function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->where('player_id', $request->player_id)
                    ->where('friend_id', $request->friend_id);
            })->orWhere(function ($q) use ($request) {
                $q->where('player_id', $request->friend_id)
                    ->where('friend_id', $request->player_id);
            });
        })->where('status', 'accepted')->first();

        if ($friend) {
            $friend->delete();
            return response()->json(['result' => 'Произошло удаление'], 200);
        }

        return response()->json(['result' => 'Ошибка, не получилось удалить'], 404);
    }
}
