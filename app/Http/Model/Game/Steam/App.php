<?php

namespace App\Http\Model\Game\Steam;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    protected string $table = 'steam_apps';

    protected array $guarded = [];

    protected array $hidden = ['id'];
}
