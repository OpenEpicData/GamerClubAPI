<?php

namespace App\Http\Controllers\Article;

use App\Http\Model\Analysis\News as AnalysisNews;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Model\Article\News;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $cache_time = !Env('APP_DEBUG') ? 60 : 0;

        $fullUrl = $request->fullUrl();

        return Cache::remember($fullUrl, $cache_time, function () use ($request) {
            $q = $request->q;
            $length = $request->length ?? 16;
            $tagName = $request->tagName;
            $refName = $request->refName;

            $top_length = $request->top_length ?? 3;

            $news = News::query();

            if ($tagName && $tagName !== 'undefined') {
                $news->whereHasIn('tag', function ($query) use ($tagName) {
                    $query->where('name', $tagName);
                });
            }

            if ($refName && $refName !== 'undefined') {
                $news->whereHasIn('ref', function ($query) use ($refName) {
                    $query->where('name', $refName);
                });
            }

            if ($q && $q !== 'undefined') {
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
                ->take($top_length)
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
                    ->latest()
                    ->first();

                array_push($top_news, $data);
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
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
