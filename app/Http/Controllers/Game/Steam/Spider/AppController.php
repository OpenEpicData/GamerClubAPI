<?php

namespace App\Http\Controllers\Game\Steam\Spider;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use QL\QueryList;
use Illuminate\Support\LazyCollection;
use App\Jobs\Game\Steam\Spider\App as AppJob;

class AppController extends Controller
{
    public function index()
    {
        $url = 'https://store.steampowered.com/search/results';
        $data = QueryList::get($url, [
            'ignore_preferences' => '1',
            'category1' => '998',
            'l' => 'schinese',
            'cc' => 'cn',
            'page' => 1
        ])
            ->rules([
                'page' => array('.search_pagination_right a', 'text'),
            ])
            ->queryData();

        $page = collect($data)->filter(function ($t) {
            return (int)$t['page'] > 1000;
        })
            ->flatten()
            ->all();

        $pages = range(1, $page[0]);

        LazyCollection::make($pages)->each(function ($t) {
            AppJob::dispatch($t);
            Log::info('已分发 AppJob:' . $t);
        });
    }
}
