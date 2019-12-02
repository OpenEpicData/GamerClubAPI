<?php

namespace App\Http\Controllers\Game\Steam;

use App\Http\Controllers\Controller;
use App\Http\Model\Game\Steam\WeeklyTopSellers;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;

class WeeklyTopSellersController extends Controller
{
    public function index() {
        $start = Carbon::now()->startOfWeek()->sub(CarbonInterval::days(1));
        return WeeklyTopSellers::whereDate('created_at', $start)
            ->oldest('rank')
            ->get();
    }
}
