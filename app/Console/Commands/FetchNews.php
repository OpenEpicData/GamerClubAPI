<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Request;

class FetchNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch News';

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
        $request = Request::create('/api/article/fetch/create', 'GET');
        $this->info(app()->make(\Illuminate\Contracts\Http\Kernel::class)->handle($request));
    }
}
