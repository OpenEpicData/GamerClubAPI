<?php

namespace App\Http\Model\Game\Steam;

use Illuminate\Database\Eloquent\Model;

class WeeklyTopSellers extends Model
{
    protected string $table = 'steam_weekly_top_sellers';

    protected array $guarded = [];

    protected array $hidden = ['id'];
}
