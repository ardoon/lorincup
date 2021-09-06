<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTournamentUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->onDelete('cascade');
            $table->foreignId('user_id')->onDelete('cascade');
            $table->enum('role', ['owner', 'admin']);
            $table->softDeletes('deleted_at', 0);
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournament_user');
    }
}
