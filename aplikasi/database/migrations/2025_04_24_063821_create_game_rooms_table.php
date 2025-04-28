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
        Schema::create('game_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('room_code',12);
            $table->unsignedBigInteger('host_user_id');
            $table->integer('max_players')->default(4);
            $table->integer('current_players')->default(0);
            $table->boolean('is_private')->default(false);
            // $table->boolean('power_up')->default(false);
            $table->string('password')->nullable();
            $table->enum('status', ['waiting', 'active', 'finished'])->default('waiting');

            $table->foreign('host_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_room');
    }
};
