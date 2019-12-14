<?php

namespace App\Http\Controllers\Queue;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Queue;

class AppController extends Controller
{
    public function index()
    {
        return Queue::size();
    }
}
