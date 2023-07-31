<?php

namespace App\Console\Commands;

use App\Models\IndexModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CreateWebCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:create-web';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建网站脚本';


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
     * @return int
     */
    public function handle()
    {
        $array = [
            "irmii.cn",
            "siaex.cn",
            ];

        $a = '<?PHP
return [';
        for ($i = 0; $i < count($array); $i++) {
            $a .= "
            '$array[$i]'=>[
                // 网站相关
                'template' => 'wdj',//模板
                'prefix_status' => 0,//泛解析 1开,0关
                'prefix_path_status' => 1,//泛目录 1开,0关
                'cache_path' => 1, //缓存 1开,0关 // 蜘蛛模式才生效
                // 百度推送相关
                'baidu_status' => 0, //是否推送 1开,0关
                'baidu_number' => 10, //每日推送条数
                'baidu_token' => 'token', //推送 token
                // 跳转相关
                'is_jump' => 1, // 1跳,0关
                'jump_hard_status' => 401, // 200, 403, 404
                'jump_url_pc' => 'https://agg.robinmalgoire.com', // pc 完整地址 http | https
                'jump_url_m' => 'https://hh.elmtreemarketing.com', // 手机 完整地址 http | https
                    ],";
        }
        $a .= '
];
';
        Storage::disk('local')->put('file.php', "$a");
    }
}
