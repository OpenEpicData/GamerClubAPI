<?php

namespace App\Http\Model\Game\Steam;

use Illuminate\Database\Eloquent\Model;

class UserCount extends Model
{
    protected string $table = 'steam_user_count';

    protected array $guarded = [];

    protected array $hidden = ['id'];
}
