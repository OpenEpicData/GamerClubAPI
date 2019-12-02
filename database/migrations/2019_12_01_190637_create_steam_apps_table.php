<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSteamAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('steam_apps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('appid')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('steam_apps', function(Blueprint $table){
            $sql = <<<SQL
                CREATE INDEX pgroonga_steam_apps_name_index ON steam_apps USING pgroonga (name);
            SQL;
            DB::connection()->getPdo()->exec($sql);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('steam_apps', function(Blueprint $table){
            $sql = <<<SQL
                DROP INDEX pgroonga_steam_apps_name_index;
            SQL;
            DB::connection()->getPdo()->exec($sql);
        });

        Schema::dropIfExists('steam_apps');
    }
}
