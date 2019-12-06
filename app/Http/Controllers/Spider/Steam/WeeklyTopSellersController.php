<?php

namespace App\Http\Controllers\Spider\Steam;

use App\Http\Controllers\Controller;
use App\Http\Model\Game\Steam\WeeklyTopSellers;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;

class WeeklyTopSellersController extends Controller
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        $res = $this->client->request('GET', 'https://store.steampowered.com/feeds/weeklytopsellers.xml?l=schinese&cc=cn');
        $xml = simplexml_load_string($res->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA);

        $json = json_encode($xml);
        $data = json_decode($json, TRUE);

        $date = $data['channel']['pubDate'];
        $item = $data['item'];

        $parse_date = Carbon::parse($date)->timestamp;

        $db_latest = WeeklyTopSellers::latest()->first();

        if (!empty($db_latest) && $db_latest->timestamp === $parse_date) {
            return;
        }

        $model = WeeklyTopSellers::query();

        for ($i = 0; $i < count($item); $i++) {
            $rank = $i + 1;
            $item_data = $item[$i];
            $model->create([
                'rank' => $rank,
                'title' => $item_data['title'],
                'link' => $item_data['link'],
                'timestamp' => $parse_date
            ]);
        }

        return '获取成功：' . $date;
    }
}
