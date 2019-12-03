<?php

namespace App\Jobs\Game\Steam\Spider;

use App\Http\Model\Game\Steam\App as AppModel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use QL\QueryList;

class App implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page;

    /**
     * Create a new job instance.
     *
     * @param $page
     */
    public function __construct($page)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Redis::throttle('SpiderSteamApp')->allow(30)->every(60)->then(function () {
            Log::info('进入队列:' . $this->page);
            $data = $this->spider();

            collect($data)->each(function ($t) {
                try {
                    AppModel::updateOrCreate(
                        [
                            'appid' =>  $t['appid'],
                            'name'  =>  $t['name']
                        ], ['appid' =>  $t['appid']]
                    );

                    Log::info('已插入' . $t['name']);
                } catch (\Exception $e) {
                    Log::info($e);
                }
            });
        }, function () {
            return $this->release(10);
        });
    }

    protected function spider()
    {
        $url = 'https://store.steampowered.com/search/results';
        $data = QueryList::get($url, [
            'ignore_preferences' => '1',
            'category1' => '998',
            'l' => 'schinese',
            'cc' => 'cn',
            'page' => $this->page
        ])
            ->rules([
                'name'  =>  array('#search_resultsRows .title', 'text'),
                'appid' =>  array('#search_resultsRows a', 'data-ds-appid'),
                'url'   =>  array('#search_resultsRows a', 'href')
            ])
            ->queryData();

        collect($data)->each(function ($t) {
            $find_string = strpos(
                $t['url']
                , 'sub'
            );


            if ($find_string) {
                Log::info($t['name'] . '-' . $t['appid']. ': 不插入集合包');
                return 0;
            }

            Log::info($t['name'] . '-' . $t['appid']. ': 处理成功');
        });

        return $data;
    }

    /**
     * 任务失败的处理过程
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        Log::info('任务失败:' . $this->page);
    }
}
