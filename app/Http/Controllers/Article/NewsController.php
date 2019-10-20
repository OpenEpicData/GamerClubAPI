<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Model\Article\News;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $fullUrl = $request->fullUrl();

        return Cache::remember($fullUrl, 0, function () use ($request) {
            $q = $request->q;
            $length = $request->length ?? 16;
            $tagName = $request->tagName;
            $refName = $request->refName;

            $news = News::query();
            
            if ($tagName) {
                $news->whereHasIn('tag', function ($query) use ($tagName) {
                    $query->where('name', $tagName);
                });
            }

            if ($refName) {
                $news->whereHasIn('ref', function ($q) use ($refName) {
                    $q->where('name', $refName);
                });
            }

            if ($q) {
                $news->where('title', 'ILIKE', "%" . $q . "%");
                $news->orWhere('description', 'ILIKE', "%" . $q . "%");
                $news->orWhere('author', 'ILIKE', "%" . $q . "%");
                $news->orWhere('game_name', 'ILIKE', "%" . $q . "%");
            }

            return $news
                ->with(['tag', 'ref'])
                ->latest()
                ->paginate($length);
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
