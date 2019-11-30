<?php

namespace App\Http\Model\Game\Steam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UserCount extends Model
{
    protected $table = 'steam_user_count';

    protected $guarded = [];

    protected $hidden = ['id'];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromDate($value)->timestamp;
    }
}
