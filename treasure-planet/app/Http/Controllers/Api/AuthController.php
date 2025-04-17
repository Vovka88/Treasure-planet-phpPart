<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Token;
use App\Models\Scores;
use Illuminate\Support\Str;

class AuthController extends Controller
{
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

    public function logout(Request $request){
        
        $request->validate([
            'token' => 'required|string',
        ]);

        $token = Token::where('token', $request->token)->delete();
        // Player::where('id', $token->player_id)->delete();
    }

    public function registration(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $playerIsReg = Player::where('email', $request->email)->first();
        
        if(empty($playerIsReg)){
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

    public function deleteUser(Request $request){
        
        $request->validate([
            'player_id' => 'required|integer',
        ]);

        $player = Player::where('id', $request->player_id)->first();
        
        if(!empty($player)){
            $player->delete();

            return response()->json(['result' => 'Аккаунт успешно удалён'], 201);
        }
        
        return response()->json(['error' => 'Не получилось удалить аккаунт'], 401);
    }

    public function getToken(Request $request){
        
        $request->validate([
            'token' => 'required|string'
        ]);

        $token = Token::where('token', $request->token)->first();
        $player = Player::where('id', $token->player_id)->first();
        

        if (!empty($token)) {
            return response()->json([
                'result' => 'Всё ок', 
                'player_id' => $token->player_id, 
                'username' => $player->username,
                'player_avatar_id' => $player->avatar_id, 
                'player_hp' => $player->hp, 
                'player_money' => $player->money], 
            201);
        }
        else {
            return response()->json(['error' => 'Произошла ошибка, не удалось вернуть айди аккаунта'], 401);
        }
    }

    public function updateUsernameAndAvatar(Request $request){

        $request->validate([
            'player_id' => 'required|integer',
            'username' => 'required|string',
            'avatar_id' => 'required|integer'
        ]);

        Player::where(['id' => $request->player_id])->update(['username' => $request->username, 'avatar_id' => $request->avatar_id]);

        return response()->json(['result' => 'Всё ок'], 201);
    }
}
