<?php

namespace App\Http\Controllers\Game\Steam;

use Illuminate\{
    Http\Request,
    Support\Carbon
};
use App\Http\Controllers\Controller;
use App\Http\Model\Game\Steam\UserCount;

class UserCountController extends Controller
{
    public function index(Request $request)
    {
        $toDaySubHours = $request->toDaySubHours ?? 0;
        $subDays = $request->subDays ?? 5;
        $data = UserCount::whereDate('created_at', '>=', Carbon::now()
            ->subDays($subDays))
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
