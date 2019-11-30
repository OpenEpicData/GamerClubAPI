<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Request;

class FetchSteamWeeklyTopSellersController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:SteamWeeklyTopSellers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'FetchSteamWeeklyTopSellers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $request = Request::create('/api/game/steam/fetch_weekly_top_sellers/create', 'GET');
        $this->info(app()->make(\Illuminate\Contracts\Http\Kernel::class)->handle($request));
    }
}