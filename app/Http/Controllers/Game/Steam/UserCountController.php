<?php

namespace App\Http\Controllers\Game\Steam;

use App\Http\Controllers\Controller;
use App\Http\Model\Game\Steam\UserCount;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class UserCountController extends Controller
{
    public function index(Request $request)
    {
        $toDaySubHours = $request->today_sub_hours ?? 0;
        $data = UserCount::whereDate('created_at', '>=', Carbon::now()
            ->startOfMonth())
            ->get();

        $user = $data->pluck('user');
        $created_at = $data->pluck('created_at');

        $today = UserCount::whereDate('created_at', '>=', Carbon::now()
            ->startOfDay()->sub($toDaySubHours, 'hours'))
            ->get();

        $avg = $today->avg('user');
        $max = $today->max('user');
        $min = $today->min('user');
        $now = $today->pluck('user')->last();

        return [
            'user' => $user,
            'created_at' => $created_at,
            'today' => [
                'avg' => $avg,
                'max' => $max,
                'min' => $min,
                'now' => $now
            ]
        ];
    }
}
