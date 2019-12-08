<?php

namespace App\Http\Controllers\Spider\Article;

use App\Http\Controllers\Controller;
use App\Http\Model\Article\{News, Ref, Tag};
use Illuminate\Support\Facades\Log;
use Goutte\Client;
use GuzzleHttp\Client as HttpClient;

class NewsController extends Controller
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var HttpClient
     */
    private HttpClient $httpClient;

    public function __construct(Client $client, HttpClient $httpClient)
    {
        $this->client = $client;
        $this->httpClient = $httpClient;
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
            $towP,
            $yys,
            $indienova,
            $vgtime
        ];
    }

    protected function towP()
    {
        $data = $this->client
            ->request('GET', 'https://www.2p.com/articles?pageSize=21&pageNo=1')
            ->filter('.game-list li')
            ->each(function ($node) {
                return [
                    'title' => $node->filter('.tit')->text(),
                    'description' => $node->filter('.summary')->text(),
                    'image' => $node->filter('.pic img')->attr('data-src'),
                    'author' => $node->filter('.user')->text(),
                    'author_avatar' => $node->filter('.user img')->attr('src'),
                    'tag' => $node->filter('.pic .tag')->text(),
                    'ref_link' => $node->filter('a')->attr('href')
                ];
            });

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

        return '2p 拉取成功';
    }

    protected function yys()
    {
        $res = $this->httpClient->request('GET', 'www.yystv.cn/boards/get_board_list_by_page?page=0&value=news');

        collect(json_decode($res->getBody())->data)->each(function ($item) {
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


        return '游研社 拉取成功';
    }

    protected function indienova()
    {
        $data = $this->client
            ->request('GET', 'https://indienova.com/channel/news')
            ->filter('.indienova-channel-border .article-panel')
            ->each(function ($node) {
                return [
                    'title' => $node->filter('h4 a')->text(),
                    'description' => $node->filter('p')->text(),
                    'image' => $node->filter('.article-image a img')->attr('src'),
                    'ref_link' => $node->filter('h4 a')->attr('href')
                ];
            });

        collect($data)->each(function ($item) {
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

        return 'indienova 拉取成功';
    }

    protected function vgtime()
    {
        $data = $this->client
            ->request('GET', 'https://www.vgtime.com/')
            ->filter('.game_news_box .vg_list li')
            ->each(function ($node) {
                return [
                    'title' => $node->filter('.info_box h2')->text(),
                    'description' => $node->filter('.info_box p')->text(),
                    'image' => $node->filter('.img_box img')->attr('data-url'),
                    'author' => $node->filter('.info_box .user_name')->text(),
                    'ref_link' => $node->filter('.info_box a')->attr('href')
                ];
            });

        collect($data)->each(function ($item) {
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

        return 'vgtime 拉取成功';
    }
}
