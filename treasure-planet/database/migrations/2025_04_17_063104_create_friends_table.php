<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('player_id');    // кто отправил запрос
            $table->unsignedBigInteger('friend_id');    // кому отправлен запрос

            $table->enum('status', ['pending', 'accepted', 'blocked'])->default('pending');

            $table->timestamps();

            $table->unique(['player_id', 'friend_id']); // нельзя добавить одного и того же друга дважды

            // Индексы и внешние ключи — если у тебя есть таблица players
            // $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            // $table->foreign('friend_id')->references('id')->on('players')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friends');
    }
};
