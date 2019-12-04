<?php

namespace App\Http\Controllers\Game\Steam;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Http\Model\Game\Steam\UserCount;

class FetchUserCountController extends Controller
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create()
    {
        $res = $this->client->request('GET', 'https://store.steampowered.com/stats/userdata.json');
        $body = json_decode($res->getBody(), true);
        $fetch_data = Arr::last($body[0]['data'])[1];

        $latest = UserCount::latest()->first('user');

        if ((int) $fetch_data !== (int) $latest['user']) {
            UserCount::create([
                'user' => $fetch_data
            ]);

            return '当前人数：' . $fetch_data . '已插入';
        }
    }
}
