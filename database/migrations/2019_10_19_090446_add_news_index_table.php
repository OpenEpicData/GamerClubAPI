<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewsIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('news', function(Blueprint $table){
            $sql = <<<SQL
                CREATE INDEX pgroonga_title_index ON news USING pgroonga (title);
                CREATE INDEX pgroonga_description_index ON news USING pgroonga (description);
                CREATE INDEX pgroonga_author_index ON news USING pgroonga (author);
                CREATE INDEX pgroonga_game_name_index ON news USING pgroonga (game_name);
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
        Schema::table('news', function(Blueprint $table){
            $sql = <<<SQL
                DROP INDEX pgroonga_title_index;
                DROP INDEX pgroonga_description_index;
                DROP INDEX pgroonga_author_index;
                DROP INDEX pgroonga_game_name_index;
            SQL;
            DB::connection()->getPdo()->exec($sql);
        });
    }
}
