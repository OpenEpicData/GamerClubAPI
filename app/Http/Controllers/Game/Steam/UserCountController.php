<?php

namespace App\Http\Controllers\Game\Steam;

use App\Http\Controllers\Controller;
use App\Http\Model\Game\Steam\UserCount;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;

class UserCountController extends Controller
{
    public function index()
    {
        $data = UserCount::whereDate('created_at', '>=', Carbon::now()
            ->startOfMonth())
            ->get();

        $user = $data->pluck('user');
        $created_at = $data->pluck('created_at');

        $avg = $data->avg('user');
        $max = $data->max('user');
        $min = $data->min('user');
        $now = $user->last();

        return [
            'user' => $user,
            'created_at' => $created_at,
            'avg' => $avg,
            'max' => $max,
            'min' => $min,
            'now' => $now
        ];
    }
}
