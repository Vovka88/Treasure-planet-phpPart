<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Player;

class PlayerController extends Controller
{
    public function getUsers()
    {
        return response()->json(Player::all());
    }

    public function getUserById($id)
    {
        $player = Player::find($id);
        if (!$player) {
            return response()->json(['message' => 'Player not found'], 404);
        }
        return response()->json($player, 200);
    }

    // 📌 Создать нового игрока
    public function createUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:players,username',
            'email' => 'required|email|unique:players,email',
            'score' => 'integer|min:0'
        ]);

        $player = Player::create($request->all());
        return response()->json($player, 201);
    }

    // 📌 Обновить данные игрока
    public function updateUser(Request $request, $id)
    {
        $player = Player::find($id);
        if (!$player) {
            return response()->json(['message' => 'Player not found'], 404);
        }

        $request->validate([
            'username' => 'string|unique:players,username,' . $id,
            'email' => 'email|unique:players,email,' . $id,
            'score' => 'integer|min:0'
        ]);

        $player->update($request->all());
        return response()->json($player, 200);
    }

    // 📌 Удалить игрока
    public function destroyUser($id)
    {
        $player = Player::find($id);
        if (!$player) {
            return response()->json(['message' => 'Player not found'], 404);
        }

        $player->delete();
        return response()->json(['message' => 'Player deleted'], 200);
    }

    public function getTokenByUserId($id){
        $player = Player::find($id);
    }

}