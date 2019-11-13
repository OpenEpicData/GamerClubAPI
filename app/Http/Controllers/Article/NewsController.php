<?php

namespace App\Http\Controllers\Article;

use App\Http\Model\Analysis\News as AnalysisNews;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Model\Article\News;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

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
                $news->whereHasIn('ref', function ($query) use ($refName) {
                    $query->where('name', $refName);
                });
            }

            if ($q) {
                $news->where(function ($query) use ($q) {
                    $query->where('title', 'ILIKE', "%" . $q . "%")
                        ->orWhere('description', 'ILIKE', "%" . $q . "%")
                        ->orWhere('author', 'ILIKE', "%" . $q . "%");
                });
            }

            $list = $news
                ->with(['tag', 'ref'])
                ->latest()
                ->paginate($length);

            $top_hit = AnalysisNews::whereDate('created_at', '>=', Carbon::now()->startOfWeek())
                ->orderBy('hit', 'desc')
                ->take(3)
                ->get();

            $top_news = [];
            foreach ($top_hit as $key) {
                $data = News::where(function ($query) use ($key) {
                    $q = $key->title;

                    $query->where('title', 'ILIKE', "%" . $q . "%")
                        ->orWhere('description', 'ILIKE', "%" . $q . "%")
                        ->orWhere('author', 'ILIKE', "%" . $q . "%");
                })
                    ->with(['tag', 'ref'])
                    ->first();

                array_push($hit_query, $data);
            }

            return response()->json([
                'top' => $top_news,
                'latest' => $list
            ]);
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
