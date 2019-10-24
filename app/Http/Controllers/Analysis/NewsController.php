<?php

namespace App\Http\Controllers\Analysis;

use App\Http\Controllers\Controller;
use App\Http\Model\Analysis\News as AnalysisNews;
use App\Http\Model\Article\News;
use Illuminate\Support\Carbon;

class NewsController extends Controller
{
    public function index()
    {
        return AnalysisNews::whereDate('created_at', Carbon::today())
            ->orderBy('hit', 'desc')
            ->get();
    }

    public function create()
    {
        News::whereDate('created_at', Carbon::today())
            ->latest()
            ->chunk(200, function ($data) {
                foreach ($data as $item) {
                    $start = stristr($item->title, '《');
                    $end = stristr($start, '》', true);
                    $title = str_replace("《", "", $end);
                    $find_repeat = AnalysisNews::where('title', $title)->get();

                    $hit = array_key_exists('hit', $find_repeat) ? $find_repeat->hit : 0;

                    if ($title) {
                        AnalysisNews::updateOrCreate(
                            ['title' => $title],
                            ['hit' => $hit + 1]
                        );
                    }
                }
            });
    }
}
