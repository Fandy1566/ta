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
        Schema::create('game_room_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_room_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('score')->default(0);
            $table->smallInteger('correct')->default(0);
            $table->smallInteger('wrong')->default(0);
            
            $table->foreign('game_room_id')->references('id')->on('game_rooms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['game_room_id', 'user_id']); // prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_room_user');
    }
};
