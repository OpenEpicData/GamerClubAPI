<?php

namespace App\Http\Model\Game\Steam;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    protected $table = 'steam_apps';

    protected $guarded = [];

    protected $hidden = ['id'];
}
