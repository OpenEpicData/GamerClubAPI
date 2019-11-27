<?php

namespace App\Http\Controllers\Game\Steam;

use App\Http\Controllers\Controller;
use App\Http\Model\Game\Steam\UserCount;
use Illuminate\Support\Carbon;

class UserCountController extends Controller
{
    public function index()
    {
        return UserCount::whereDate('created_at', '>=', Carbon::now()
            ->startOfMonth())
            ->latest()
            ->get();
    }
}
