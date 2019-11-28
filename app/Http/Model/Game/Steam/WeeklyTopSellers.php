<?php

namespace App\Http\Model\Game\Steam;

use Illuminate\Database\Eloquent\Model;

class WeeklyTopSellers extends Model
{
    protected $table = 'steam_weekly_top_sellers';

    protected $guarded = [];

    protected $hidden = ['id'];
}
