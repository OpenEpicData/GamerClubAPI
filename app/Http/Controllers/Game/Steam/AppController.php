<?php

namespace App\Http\Controllers\Game\Steam;

use App\Http\Controllers\Controller;
use App\Http\Model\Game\Steam\App as AppModel;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index(Request $request)
    {
        return AppModel::paginate($request->length);
    }
}
