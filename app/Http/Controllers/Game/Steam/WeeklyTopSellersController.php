<?php

namespace App\Http\Controllers\Game\Steam;

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Model\Game\Steam\WeeklyTopSellers;
use Illuminate\Http\Request;

class WeeklyTopSellersController extends Controller
{
    public function index(Request $request) {
        $subWeek = $request->subWeek ?? 0;
        $start = Carbon::now()->startOfWeek()->subWeek($subWeek)->sub(CarbonInterval::days(1));
        return WeeklyTopSellers::whereDate('created_at', $start)
            ->oldest('rank')
            ->get();
    }
}
