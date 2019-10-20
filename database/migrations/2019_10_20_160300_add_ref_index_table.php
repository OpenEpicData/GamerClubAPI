<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('refs', function(Blueprint $table){
            $sql = <<<SQL
                CREATE INDEX pgroonga_refs_name_index ON refs USING pgroonga (name);
                CREATE INDEX pgroonga_refs_top_domain_index ON refs USING pgroonga (top_domain);
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
        Schema::table('refs', function(Blueprint $table){
            $sql = <<<SQL
                ALTER TABLE refs DROP INDEX pgroonga_refs_name_index;
                ALTER TABLE refs DROP INDEX pgroonga_refs_top_domain_index;
            SQL;
            DB::connection()->getPdo()->exec($sql);
        });
    }
}
