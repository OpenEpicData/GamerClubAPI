<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Model\{
    Article\News,
    Analysis\News as AnalysisNews
};
use Illuminate\Http\{
    Request,
    Response
};
use Illuminate\Support\{
    Facades\Cache,
    Carbon
};

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

            $top_length = $request->topLength ?? 3;

            $news = News::query();

            if ($tagName && $tagName !== 'undefined') {
                $news->whereHasIn('tag', fn($query) => $query->where('name', $tagName));
            }

            if ($refName && $refName !== 'undefined') {
                $news->whereHasIn('ref', fn($query) => $query->where('name', $refName));
            }

            if ($q && $q !== 'undefined') {
                $news->where(
                    fn($query) =>
                        $query->where('title', 'ILIKE', "%" . $q . "%")
                            ->orWhere('description', 'ILIKE', "%" . $q . "%")
                            ->orWhere('author', 'ILIKE', "%" . $q . "%")
                );
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
                $data = News::where(
                    fn($query) =>
                        $query->where('title', 'ILIKE', "%" . $key->title . "%")
                            ->orWhere('description', 'ILIKE', "%" . $key->title . "%")
                            ->orWhere('author', 'ILIKE', "%" . $key->title . "%")
                )
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
}
