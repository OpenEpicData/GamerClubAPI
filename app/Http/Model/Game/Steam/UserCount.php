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
        $data =  Carbon::createFromDate($value);
        return $data->timestamp . $data->milli;
    }
}
