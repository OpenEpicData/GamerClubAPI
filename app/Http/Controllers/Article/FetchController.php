<?php

namespace App\Http\Controllers\Article;

use QL\QueryList;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Http\Model\Article\{
    News,
    Ref,
    Tag
};
use Illuminate\Support\Facades\Log;

class FetchController extends Controller
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return News::with(['tag', 'ref'])->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $towP = $this->towP();
        $yys = $this->yys();


        return [
            '2p' => $towP,
            '游研社' => $yys
        ];
    }

    protected function towP()
    {
        $data = QueryList::get('https://www.2p.com/articles', [
            'pageSize' => '21',
            'pageNo' => '1'
        ])
            ->rules([
                'title' => array('.game-list li .tit', 'text'),
                'description' => array('.game-list li .summary', 'text'),
                'image' => array('.game-list li .pic img', 'data-src'),
                'author' => array('.game-list li .user', 'text'),
                'author_avatar' => array('.game-list li .user img', 'src'),
                'game_name' => array('.game-list li .game', 'text'),
                'tag' => array('.game-list li .pic .tag', 'text'),
                'ref_link' => array('.game-list li a', 'href')
            ])
            ->queryData();

        $collection = collect($data)->each(function ($item, $key) {
            $title = $item['title'];
            $tag = $item['tag'];
            $ref_name = '2p';
            $ref_top_domain = '//www.2p.com';

            $tag_id = Tag::firstOrCreate(
                ['name' => $tag],
                ['name' => $tag]
            )->id;

            $ref_id = Ref::firstOrCreate(
                ['name' => $ref_name],
                [
                    'name' => $ref_name,
                    'top_domain' => $ref_top_domain
                ]
            )->id;

            News::firstOrCreate(
                ['title' => $title],
                [
                    'title' => $title,
                    'description' => $item['description'] ?? null,
                    'image' => $item['image'] ?? null,
                    'author' => $item['author'] ?? null,
                    'author_avatar' => $item['author_avatar'] ?? null,
                    'game_name' => $item['game_name'] ?? null,
                    'tag_id' => $tag_id,
                    'ref_id' => $ref_id,
                    'ref_link' => $item['ref_link']
                ]
            );
            Log::info('拉取文章: ' . $ref_name . ' - ' . $title);
        });

        return '2p fecth success';
    }

    protected function yys()
    {
        $res = $this->client->request('GET', 'https://www.yystv.cn/boards/get_board_list_by_page?page=0&value=news');

        $collection = collect(json_decode($res->getBody())->data)->each(function ($item, $key) {
            $title = $item->title;
            $tag = '趣闻';
            $ref_name = '游研社';
            $ref_top_domain = '//www.yystv.cn';

            $tag_id = Tag::firstOrCreate(
                ['name' => $tag],
                ['name' => $tag]
            )->id;

            $ref_id = Ref::firstOrCreate(
                ['name' => $ref_name],
                [
                    'name' => $ref_name,
                    'top_domain' => $ref_top_domain
                ]
            )->id;

            News::firstOrCreate(
                ['title' => $title],
                [
                    'title' => $title,
                    'description' => $item->preface ?? null,
                    'image' => $item->cover ?? null,
                    'author' => $item->author ?? null,
                    'tag_id' => $tag_id,
                    'ref_id' => $ref_id,
                    'ref_link' => '//www.yystv.cn/p/' . $item->id
                ]
            );
            Log::info('拉取文章: ' . $ref_name . ' - ' . $title);
        });


        return 'yys fetch success';
    }
}
