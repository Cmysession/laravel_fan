<?php

namespace App\Console\Commands;

use App\Models\IndexModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BaiduZzCommand extends Command
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

    public $web_model = [];
    public $web_index = 0;
    public $web_keys = [];

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
        $this->web_model = config('web');
        $this->web_keys = array_keys($this->web_model);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->error('开始推送百度站长');
        $this->run_baidu_zz();
    }

    public function run_baidu_zz()
    {
        if ($this->web_index > count($this->web_keys) - 1) {
            $this->warn('推送完成');
            die;
        }
        $web_host = $this->web_keys[$this->web_index];
        $web_info = $this->web_model[$web_host];
        if ($web_info['baidu_status'] === 1 && $web_info['baidu_token'] !== '') {

            $this->info("开始推送:$web_host,共{$web_info['baidu_number']}条");
        }
        ++$this->web_index;
        $this->run_baidu_zz();
    }

    /**
     * @param array $urls
     * @param string $site
     * @param string $token
     * @return void
     */
    public function baidu_put(array $urls = [], string $site, string $token)
    {
        $urls = array(
            'http://www.example.com/1.html',
            'http://www.example.com/2.html',
        );
        $api = 'http://data.zz.baidu.com/urls?site='.$site.'&token=' . $token;
        $ch = \curl_init();
        $options = array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        echo $result;
    }
}
