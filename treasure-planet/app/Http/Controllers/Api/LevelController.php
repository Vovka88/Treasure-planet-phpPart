<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Token;
use App\Models\Scores;

class LevelController extends Controller
{
    public function getLevelStats(Request $request){
        $request->validate([
            'player_id' => "required|numeric",
            'level_id' => "required|numeric"
        ]);
        
        $level_score = Scores::where('player_id', $request->player_id)->where('level_id', $request->level_id)->first();

        if(!empty($level_score)){
            return response()->json($level_score, 201);
        }

        return response()->json(['error' => 'Неполучилось найти'], 401);
    }

    public function getLevelsByPlayerId(Request $request){
        $request->validate([
            'player_id' => "required|numeric"
        ]);

        $levels = Scores::where('player_id', $request->player_id)->orderBy('level_id', 'desc')->get();

        if(!empty($levels)){
            return response()->json(['levels' => $levels], 201);
        }

        return response()->json(['error' => 'Неполучилось найти'], 401);
    }

    public function saveLevelData(Request $request){
        $request->validate([
            'player_id' => 'required|numeric',
            'level_id' => 'required|numeric',
            'score' => 'required|numeric',
            'count_of_stars' => 'required|numeric',
            'completed' => 'required|numeric',
        ]);

        $level_score = Scores::updateOrCreate(
            ['player_id' => $request->player_id, 'level_id' => $request->level_id],
            ['score' => $request->score, 'count_of_stars' => $request->count_of_stars, 'completed' => $request->completed]
        );

        if ($level_score) {
            return response()->json(['result' => 'Данные сохранены'], 201);
        } else {
            return response()->json(['error' => 'Не получилось сохранить'], 500);
        }
    }
}
