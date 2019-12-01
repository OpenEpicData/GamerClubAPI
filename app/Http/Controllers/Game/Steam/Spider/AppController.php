<?php

namespace App\Http\Controllers\Game\Steam\Spider;
use App\Http\Controllers\Controller;
use Illuminate\Support\LazyCollection;
use GuzzleHttp\Client;

class AppController extends Controller
{
    public function index(Client $client)
    {
        $res = $client->get('https://api.steampowered.com/ISteamApps/GetAppList/v0002/')->getBody();
        $body = json_decode($res, true);
        return LazyCollection::make($body['applist']['apps'])
            ->chunk(200)
            ->all();
    }
}
