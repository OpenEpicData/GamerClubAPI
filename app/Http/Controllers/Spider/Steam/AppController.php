<?php

namespace App\Http\Controllers\Spider\Steam;

use App\Http\Controllers\Controller;
use App\Jobs\Game\Steam\Spider\App as AppJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use Goutte\Client;

class AppController extends Controller
{
    public function index(Client $client)
    {
        $crawler = $client->request('GET', 'https://store.steampowered.com/search/results');
        $data = $crawler->filter('.search_pagination_right a')->each(fn($node) =>
            $node->text()
        );

        $page = collect($data)->filter(function ($t) {
            return (int)$t > 1000;
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
