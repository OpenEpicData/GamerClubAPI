<?php

namespace App\Http\Controllers\Analysis;

use App\Http\Controllers\Controller;
use App\Http\Model\Analysis\News as AnalysisNews;
use App\Http\Model\Article\News;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $length = $request->length ?? 16;

        return AnalysisNews::whereDate('created_at', '>=', Carbon::now()->startOfWeek())
            ->orderBy('hit', 'desc')
            ->latest()
            ->paginate($length);
    }

    public function create()
    {
        News::whereDate('created_at', '>=', Carbon::now()->startOfWeek())
            ->latest()
            ->chunk(
                200,
                function ($data) {
                    foreach ($data as $item) {
                        $start = stristr($item->title, '《');
                        $end = stristr($start, '》', true);
                        $title = str_replace("《", "", $end);

                        $hit = News::where(function($query) use ($title) {
                            $query->where('title', 'ILIKE', "%" . $title . "%")
                            ->orWhere('description', 'ILIKE', "%" . $title . "%")
                            ->orWhere('author', 'ILIKE', "%" . $title . "%")
                            ->orWhere('game_name', 'ILIKE', "%" . $title . "%");
                        })->count();
                        
                        if ($title) {
                            AnalysisNews::updateOrCreate(
                                ['title' => $title],
                                ['hit' => $hit]
                            );
                        }
                    }
                }
            );
    }
}
