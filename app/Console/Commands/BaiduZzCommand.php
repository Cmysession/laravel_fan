<?php

namespace App\Console\Commands;

use App\Models\IndexModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\RotatingFileHandler;
use function PHPUnit\Framework\isFalse;

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
    public $indexModel = null;
    public $prefix_site_array = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->indexModel = new IndexModel();
        $this->prefix_array = $this->indexModel->prefix_array;
        $this->request_url_array = $this->indexModel->request_url_array;
        $this->nickname = $this->indexModel->nickname;
        $this->web_model = config('web') ?? [];
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
        $prefix_site_array = $this->web_model[$web_host]['prefix_site_array'];
        if ($web_info['baidu_status'] === 1 && $web_info['baidu_token'] !== '') {
            $url_array = [];
            $prefix_str = 'www';
            $request_url_array = $this->indexModel->get_query($this->web_model[$web_host]['template']);
            if (count($prefix_site_array)) {
                $prefix_str = $prefix_site_array[rand(0, count($prefix_site_array) - 1)];
            }
            $site = '';
            $token = $web_info['baidu_token'];
            // 发布几条
            for ($i = 0; $i < $web_info['baidu_number']; $i++) {
                $url = '';
                // 判断是否泛域名
                if ($web_info['prefix_status'] === 1) {
                    $site = $prefix_str . '.' . $web_host;
                } else {
                    $site = 'www.' . $web_host;
                }
                // 判断是否泛目录
                if ($web_info['prefix_path_status'] === 1) {
                    $url .= $site . '/' . $request_url_array[rand(0, count($request_url_array) - 1)] . '/' . rand(0, 999999) . '.html';
                }
                $url_array[] = $url;
            }
            $this->info("开始推送:$site,token:{$token},共{$web_info['baidu_number']}条");
            $baidu_put = $this->baidu_put($url_array, $site, $token);
            if (empty($baidu_put['success'])) {
                $message = '地址有误!或网站错误!';
                if (!empty($baidu_put['message'])) {
                    $message = $baidu_put['message'];
                }
                // 推送失败的
                (new \Monolog\Logger('local'))
                    ->pushHandler(new RotatingFileHandler(storage_path("baidu-zz-logs/error.log")))
                    ->info("推送出错:$message,域名:$site,token:{$token},共{$web_info['baidu_number']}条", $url_array);
            }else{
                // 推送成功
                (new \Monolog\Logger('local'))
                    ->pushHandler(new RotatingFileHandler(storage_path("baidu-zz-logs/success.log")))
                    ->info("推送成功:$site,token:{$token},共{$web_info['baidu_number']}条",$baidu_put);
            }
        }
        ++$this->web_index;
        $this->run_baidu_zz();
    }

    /**
     * @param array $urls
     * @param string $site
     * @param string $token
     */
    public function baidu_put(array $urls, string $site, string $token)
    {
        $api = 'http://data.zz.baidu.com/urls?site=' . $site . '&token=' . $token;
        try {
            $ch = \curl_init();
            $options = array(
                CURLOPT_URL => $api,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => implode("\n", $urls),
                CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
            );
            curl_setopt_array($ch, $options);
            return json_decode(curl_exec($ch), true);
        } catch (\Exception $exception) {
            return json_encode(['message' => $exception->getMessage()]);
        }

    }

    
    /**
     * 随机字符
     */
    public function get_rand_str(): string
    {
        //字符组合
        $str = 'abcdefghijklmnopqrstuvwxyz';
        $len = strlen($str) - 1;
        $length = rand(3, 5);
        $randstr = '';
        for ($i = 0; $i < $length; $i++) {
            $num = mt_rand(0, $len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }
}
