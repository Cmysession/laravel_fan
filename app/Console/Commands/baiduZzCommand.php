<?php

namespace App\Console\Commands;

use App\Http\Controllers\Web\IndexController;
use App\Models\IndexModel;
use Illuminate\Console\Command;

class baiduZzCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:baidu-zz';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '百度站长统计';

    /**
     * 泛域名前缀
     * @var string[]
     */
    public $prefix_array = [];

    /**
     * 种类|类别
     * @var string[]
     */
    public $request_url_array = [];

    public $nickname = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $indexModel = new IndexModel();
        $this->prefix_array = $indexModel->prefix_array;
        $this->request_url_array = $indexModel->request_url_array;
        $this->nickname = $indexModel->nickname;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

    }
}
