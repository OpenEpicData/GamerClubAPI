<?php

namespace App\Http\Controllers\Game\Steam;

use App\Http\Controllers\Controller;
use App\Http\Model\Game\Steam\UserCount;
use Illuminate\Support\Carbon;

class UserCountController extends Controller
{
    public function index()
    {
        $data = UserCount::whereMonth(
            'created_at',
            Carbon::now()->month
        )
            ->get();

        $user = $data->pluck('user');
        $created_at = $data->pluck('created_at');

        $today = UserCount::whereDay (
            'created_at',
            Carbon::today()
        )
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
