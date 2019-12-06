<?php

namespace App\Http\Controllers\Spider\Article;

use App\Http\Controllers\Controller;
use App\Http\Model\Article\{News, Ref, Tag};
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use QL\QueryList;

class NewsController extends Controller
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return array
     */
    public function index()
    {
        $towP = $this->towP();
        $yys = $this->yys();
        $indienova = $this->indienova();
        $vgtime = $this->vgtime();


        return [
            '2p' => $towP,
            '游研社' => $yys,
            'indienova' => $indienova,
            'vgtime' => $vgtime
        ];
    }

    protected function towP()
    {
        $data = QueryList::get('www.2p.com/articles', [
            'pageSize' => '21',
            'pageNo' => '1'
        ])
            ->rules([
                'title' => array('.game-list li .tit', 'text'),
                'description' => array('.game-list li .summary', 'text'),
                'image' => array('.game-list li .pic img', 'data-src'),
                'author' => array('.game-list li .user', 'text'),
                'author_avatar' => array('.game-list li .user img', 'src'),
                'tag' => array('.game-list li .pic .tag', 'text'),
                'ref_link' => array('.game-list li a', 'href')
            ])
            ->queryData();

        collect($data)->each(function ($item) {
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
                    'tag_id' => $tag_id,
                    'ref_id' => $ref_id,
                    'ref_link' => $item['ref_link']
                ]
            );
            Log::info('拉取文章: ' . $ref_name . ' - ' . $title);
        });

        return '2p fetch success';
    }

    protected function yys()
    {
        $res = $this->client->request('GET', 'www.yystv.cn/boards/get_board_list_by_page?page=0&value=news');

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

    protected function indienova()
    {
        $data = QueryList::get('indienova.com/channel/news')
            ->rules([
                'title' => array('.indienova-channel-border .article-panel h4 a', 'text'),
                'description' => array('.indienova-channel-border .article-panel p', 'text'),
                'image' => array('.indienova-channel-border .article-panel .article-image a img', 'src'),
                'ref_link' => array('.indienova-channel-border .article-panel h4 a', 'href')
            ])
            ->queryData();

        $collection = collect($data)->each(function ($item, $key) {
            $title = $item['title'];
            $tag = '资讯';
            $ref_name = 'indienova';
            $ref_top_domain = '//indienova.com';
            $image = str_replace("_t205", "", $item['image'] ?? null) ?? null;

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
                    'image' => $image ?? null,
                    'author' => $item['author'] ?? null,
                    'author_avatar' => $item['author_avatar'] ?? null,
                    'tag_id' => $tag_id,
                    'ref_id' => $ref_id,
                    'ref_link' => $ref_top_domain . $item['ref_link']
                ]
            );
            Log::info('拉取文章: ' . $ref_name . ' - ' . $title);
        });

        return 'indienova fecth success';
    }

    protected function vgtime()
    {
        $res = $this->client->request('GET', 'http://www.vgtime.com/');
        $data = QueryList::html($res->getBody())
            ->rules([
                'title' => array('.game_news_box .vg_list .info_box h2', 'text'),
                'description' => array('.game_news_box .vg_list .info_box p', 'text'),
                'image' => array('.game_news_box .vg_list .img_box img', 'data-url'),
                'author' => array('.game_news_box .vg_list .info_box .user_name', 'text'),
                'ref_link' => array('.game_news_box .vg_list .info_box a', 'href')
            ])
            ->queryData();

        $collection = collect($data)->each(function ($item, $key) {
            $title = $item['title'];
            $tag = '资讯';
            $ref_name = 'vgtime';
            $ref_top_domain = '//www.vgtime.com';
            $image = str_replace("?x-oss-process=image/resize,m_pad,color_000000,w_640,h_400", "", $item['image'] ?? null) ?? null;

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
                    'image' => $image ?? null,
                    'author' => $item['author'] ?? null,
                    'author_avatar' => $item['author_avatar'] ?? null,
                    'tag_id' => $tag_id,
                    'ref_id' => $ref_id,
                    'ref_link' => $ref_top_domain . $item['ref_link']
                ]
            );
            Log::info('拉取文章: ' . $ref_name . ' - ' . $title);
        });

        return 'vgtime fecth success';
    }
}
