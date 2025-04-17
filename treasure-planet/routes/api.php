<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\FriendsController;

// Работа с аккаунтом
Route::post('/login', [AuthController::class, 'login']);
Route::post('/loginByToken', [AuthController::class, 'getToken']);
Route::post('/logout', [AuthController::class, 'registration']);
Route::post('/registration', [AuthController::class, 'registration']);
Route::post('/deleteaccount', [AuthController::class, 'deleteUser']);
Route::post('/updateusername', [AuthController::class, 'updateUsernameAndAvatar']);

// Работа с уровнями
Route::post('/getstats', [LevelController::class, 'getLevelStats']);
Route::post('/getlevels', [LevelController::class, 'getLevelsByPlayerId']);
Route::post('/savelevelstats', [LevelController::class, 'saveLevelData']);

// Работа с друзьями
Route::post('/getfriends', [FriendsController::class, 'getFriends']);
Route::post('/getfriendsinvites', [FriendsController::class, 'getInvitesToFriends']);
Route::post('/sendfriendinvite', [FriendsController::class, 'sendFriendInvite']);
Route::post('/acceptfriendinvite', [FriendsController::class, 'acceptFriendInvite']);
Route::post('/deletefriend', [FriendsController::class, 'deleteFriend']);