<?php

namespace App\Http\Controllers\Game\Steam;

use App\Http\Controllers\Controller;
use App\Http\Model\Game\Steam\WeeklyTopSellers;
use Illuminate\Support\Carbon;

class WeeklyTopSellersController extends Controller
{
    public function index() {
        $data = WeeklyTopSellers::whereDate('created_at', '>=', Carbon::now()
            ->startOfWeek())
            ->oldest('rank')
            ->get();

        return $data;
    }
}
