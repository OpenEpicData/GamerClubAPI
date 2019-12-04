<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Model\Article\Ref;
use Illuminate\Http\Response;

class RefController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return Ref::pluck('name');
    }
}
