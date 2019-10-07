<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 100);
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('author', 60)->nullable();
            $table->string('author_avatar', 60)->nullable();
            $table->string('game_name', 60)->nullable();
            $table->integer('tag_id')->nullable();
            $table->integer('ref_id')->nullable();
            $table->string('ref_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
