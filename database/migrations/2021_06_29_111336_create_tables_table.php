<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->onDelete('cascade');
            $table->json('schema')->nullable();
            $table->enum('type', ['single', 'double', 'round']);
            $table->enum('status', ['raw', 'started', 'finished'])->default('raw');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
            $table->foreign('tournament_id')->references('id')->on('tournaments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tables');
    }
}
