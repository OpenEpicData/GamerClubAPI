<?php

namespace App\Http\Controllers\Game\Steam;

use App\Http\Controllers\Controller;
use App\Http\Model\Game\Steam\UserCount;
use Illuminate\Support\Carbon;

class UserCountController extends Controller
{
    public function index()
    {
        $data = UserCount::whereDate('created_at', '>=', Carbon::now()
            ->startOfMonth())
            ->latest()
            ->get();

        $user = $data->pluck('user');
        $created_at = $data->pluck('created_at');

        return [
            'user' => $user,
            'created_at' => $created_at
        ];
    }
}
