<?php

/**
 * @SuppressWarnings("App\Http\Middleware\Cors")
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Friends;
use App\Models\Player;

class FriendsController extends Controller
{
    public function getFriends(Request $request){
        $request->validate([
            'player_id' => 'required|numeric'
        ]);
        
        $friends = Friends::where(['player_id' => $request->player_id, 'status' => 'accepted'])->get();
        $players = array();
        foreach ($friends as $friend) {
            # code...
            array_push($players, Player::where(['id' => $friend->friend_id])->first());
        }
        
        return response()->json(['players' => $players], 201);
    }

    public function getInvitesToFriends(Request $request){
        $request->validate([
            'player_id' => 'required|numeric',
        ]);

        $friends = Friends::where(['player_id' => $request->player_id, 'status' => 'pending'])->get();

        return response()->json(['friends' => $friends], 201);
    }
    
    public function sendFriendInvite(Request $request){
        $request->validate([
            'player_id' => 'required|numeric',
            'friend_id' => 'required|numeric'
        ]);
        

        $friends = Friends::where(['player_id' => $request->player_id, 'friend_id' => $request->friend_id])->get();

        if(empty($friends)){
            $friend = Friends::create(['player_id' => $request->player_id, 'friend_id' => $request->friend_id, 'status' => 'pending']);
        }
        
        if(!empty($friend)){
            return response()->json(['result' => 'Заявка отправлена'], 201);
        }
        else{
            return response()->json(['error' => 'Ошибка отправки'], 401);
        }
    }

    public function acceptFriendInvite(Request $request){
        $request->validate([
            'player_id' => 'required|numeric',
            'friend_id' => 'required|numeric'
        ]);

        $friend = Friends::where(['player_id' => $request->player_id, 'friend_id' => $request->friend_id,  'status' => 'pending'])->first();
        $friend->update(['status' => 'accepted']);

        if(!empty($friend)){
            return response()->json(['result' => 'Друг добавлен'], 201);
        }
        else{
            return response()->json(['error' => 'Ошибка добавления'], 401);
        }
    }

    public function deleteFriend(Request $request){
        $request->validate([
            'player_id' => 'required|numeric',
            'friend_id' => 'required|numeric'
        ]);

        $friend = Friends::where(['player_id' => $request->player_id, 'friend_id' => $request->friend_id,  'status' => 'accepted'])->first();
        $friend->delete();

        if(empty($friend)){
            return response()->json(['result' => 'Произошло удаление'], 201);
        }
        else{
            return response()->json(['error' => 'Ошибка удаление'], 401);
        }
    }
}
