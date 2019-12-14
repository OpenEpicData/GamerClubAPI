<?php

namespace App\Http\Controllers\Spider\Steam;

use App\Http\Controllers\Controller;
use App\Jobs\Game\Steam\Spider\App as AppJob;
use Goutte\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class AppController extends Controller
{
    public function index(Client $client)
    {
        $url = 'https://store.steampowered.com/search/results?ignore_preferences=1&category1=998&l=schinese&cc=cn&page=1';
        $data = $client->request('GET', $url)
            ->filter('.search_pagination_right a')
            ->each(function ($node) {
                return $node->text();
            });

        $page = collect($data)->filter(fn($t) => (int)$t > 1000)
            ->flatten()
            ->all();

        $pages = range(1, $page[0]);

        LazyCollection::make($pages)->each(function ($t) {
            AppJob::dispatch($t);
            Log::info('已分发 AppJob:' . $t);
        });
    }
}
