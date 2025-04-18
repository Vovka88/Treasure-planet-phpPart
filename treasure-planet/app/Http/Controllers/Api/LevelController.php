<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Scores;

class LevelController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/getstats",
     *     summary="Получить статистику уровня для игрока",
     *     tags={"Levels"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id", "level_id"},
     *             @OA\Property(property="player_id", type="integer", example=1),
     *             @OA\Property(property="level_id", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Успешный ответ",
     *         @OA\JsonContent(ref="#/components/schemas/Score")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Ошибка: Не получилось найти"
     *     )
     * )
     */

    public function getLevelStats(Request $request)
    {
        $request->validate([
            'player_id' => "required|numeric",
            'level_id' => "required|numeric"
        ]);

        $level_score = Scores::where('player_id', $request->player_id)->where('level_id', $request->level_id)->first();

        if (!empty($level_score)) {
            return response()->json($level_score, 201);
        }

        return response()->json(['error' => 'Неполучилось найти'], 401);
    }

    /**
     * @OA\Post(
     *     path="/api/getlevels",
     *     summary="Получить список уровней игрока",
     *     tags={"Levels"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id"},
     *             @OA\Property(property="player_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Список уровней игрока",
     *         @OA\JsonContent(
     *             @OA\Property(property="levels", type="array", @OA\Items(ref="#/components/schemas/Score"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Ошибка: Не получилось найти"
     *     )
     * )
     */

    public function getLevelsByPlayerId(Request $request)
    {
        $request->validate([
            'player_id' => "required|numeric"
        ]);

        $levels = Scores::where('player_id', $request->player_id)->orderBy('level_id', 'desc')->get();

        if (!empty($levels)) {
            return response()->json(['levels' => $levels], 201);
        }

        return response()->json(['error' => 'Неполучилось найти'], 401);
    }

    /**
     * @OA\Post(
     *     path="/api/savelevelstats",
     *     summary="Сохранить статистику уровня для игрока",
     *     tags={"Levels"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id", "level_id", "score", "count_of_stars", "completed"},
     *             @OA\Property(property="player_id", type="integer", example=1),
     *             @OA\Property(property="level_id", type="integer", example=2),
     *             @OA\Property(property="score", type="integer", example=1500),
     *             @OA\Property(property="count_of_stars", type="integer", example=3),
     *             @OA\Property(property="completed", type="integer", enum={0,1}, example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Успешно сохранено",
     *         @OA\JsonContent(
     *             @OA\Property(property="result", type="string", example="Данные сохранены")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка сохранения"
     *     )
     * )
     */

    public function saveLevelData(Request $request)
    {
        // Валидация входных данных
        $request->validate([
            'player_id' => 'required|numeric',
            'level_id' => 'required|numeric',
            'score' => 'required|numeric|min:0',  // Минимум 0
            'count_of_stars' => 'required|numeric|min:0|max:3',  // 0 - 5 звезд
            'completed' => 'required|numeric|in:0,1',  // 0 или 1
        ]);

        // Обновление или создание записи
        $level_score = Scores::updateOrCreate(
            ['player_id' => $request->player_id, 'level_id' => $request->level_id],
            [
                'score' => $request->score,
                'count_of_stars' => $request->count_of_stars,
                'completed' => $request->completed
            ]
        );

        // Проверка на успешное сохранение
        if ($level_score) {
            return response()->json(['result' => 'Данные сохранены', 'pl_id' => $request->player_id, 'lvl_id' => $request->level_id, 'score' => $request->score, 'count_of_starts' => $request->count_of_stars, 'completed' => $request->completed], 201);
        } else {
            return response()->json(['error' => 'Не получилось сохранить'], 500);
        }
    }
}
