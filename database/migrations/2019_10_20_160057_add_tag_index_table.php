<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTagIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tags', function(Blueprint $table){
            $sql = <<<SQL
                CREATE INDEX pgroonga_tags_name_index ON tags USING pgroonga (name);
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
        Schema::table('tags', function(Blueprint $table){
            $sql = <<<SQL
                ALTER TABLE tags DROP INDEX pgroonga_tags_name_index;
            SQL;
            DB::connection()->getPdo()->exec($sql);
        });
    }
}
