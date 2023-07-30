<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\IndexModel;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\RotatingFileHandler;

class IndexController extends Controller
{

    public $template = null;

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
     * 是否开启泛域名
     * @var int
     */
    public $prefix_status = 0;

    public $prefix_path_status = 0;

    public $prefix_title = '';

    public $cache_path = 'cache';

    public $model = null;

    public $host = '';

    public $bot_spider = false;


    /**
     * "{固定关键词}"
     * "{随机关键词}"
     * "{随机图片}"
     * "{固定图片}"
     * "{随机列表链接}"
     * "{随机详情链接}"
     * "{随机网名}"
     * "{随机数字}"
     * "{随机时间}"
     * "{随机小数点}"
     * @throws FileNotFoundException
     */

    public function __construct(Request $request)
    {
        $web_config = config('web');
        $this->host = $_SERVER['HTTP_HOST'];
        $web_keys = array_keys($web_config);
        $host = '';
        for ($i = 0; $i < count($web_keys); $i++) {
            if (!strpos($this->host, $web_keys[$i])) {
                die("<h2 style='text-align: center'> 网站未配置 01 </h2>");
            }
            $host = $web_keys[$i];
            break;
        }
        $this->host = $host;
        $this->model = $web_config[$this->host];
        $indexModel = new IndexModel();
        $this->prefix_array = $indexModel->prefix_array;
        $this->request_url_array = $indexModel->request_url_array;
        $this->nickname = $indexModel->nickname;
        // 模板
        $this->template = $this->model['template'] ?? die("<h2 style='text-align: center'> 网站未配置 template </h2>");
        $this->prefix_status = $this->model['prefix_status'] ?? die("<h2 style='text-align: center'> 网站未配置 prefix_status </h2>");
        $this->prefix_path_status = $this->model['prefix_path_status'] ?? die("<h2 style='text-align: center'> 网站未配置 prefix_path_status </h2>");
        $this->cache_path = "public/template/$this->template/" . $this->cache_path . '/' . str_replace(".", "_", str_replace(":", "_", $this->host)) . '/' . $_SERVER['REQUEST_URI'];
        if (substr(strrchr($this->cache_path, '.'), 1) !== 'html') {
            $this->cache_path .= '.html';
        }
        if ($this->model['cache_path']) {
            $html = $this->get_file_html($this->cache_path);
            // 缓存存在直接输出
            if ($html) {
                die($html);
            }
        }

        $this->spider($request->userAgent(), $request->url(), $request->ip());

        // 标题
        $prefix_title = mb_substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], '.'));
        if ($prefix_title) {
            if (!empty($this->prefix_array[$prefix_title])) {
                $this->prefix_title = $this->prefix_array[$prefix_title];
            }
        }
    }

    /**
     * 主页
     * @return void
     */
    public function index(): void
    {
        // 缓存查询
        $index_html = @file_get_contents(storage_path("app/public/template/$this->template/index.html"));
        if (!$index_html) {
            die("<h2 style='text-align: center'> index.html </h2>");
        }
        $index_html = $this->exchange($index_html);
        if ($this->bot_spider) {
            if ($this->model['cache_path']) {
                $this->put_file_html($this->cache_path, $index_html);
            }
        }

        die($index_html);

    }

    /**
     * 列表页
     * @return void
     */
    public function list(): void
    {
        $index_html = @file_get_contents(storage_path("app/public/template/$this->template/list.html"));
        if (!$index_html) {
            die("<h2 style='text-align: center'> list.html </h2>");
        }
        $index_html = $this->exchange($index_html);
        if ($this->bot_spider) {
            if ($this->model['cache_path']) {
                $this->put_file_html($this->cache_path, $index_html);
            }
        }
        die($index_html);
    }

    /**
     * 详情页
     * @return void
     */
    public function row(): void
    {

        $index_html = @file_get_contents(storage_path("app/public/template/$this->template/row.html"));
        if (!$index_html) {
            die("<h2 style='text-align: center'> row.html </h2>");
        }
        $index_html = $this->exchange($index_html);
        if ($this->bot_spider) {
            if ($this->model['cache_path']) {
                $this->put_file_html($this->cache_path, $index_html);
            }
        }
        die($index_html);
    }

    public function exchange(string $html): string
    {
        $html = $this->exchange_key_all($html);
        $html = $this->exchange_list_link($html);
        $html = $this->exchange_row_link($html);
        $html = $this->exchange_number($html);
        $html = $this->exchange_date($html);
        $html = $this->exchange_nickname($html);
        $html = $this->exchange_content($html);
        $html = $this->exchange_number_n($html);
        $html = $this->exchange_img($html);
        return $html;
    }

    /**
     * 固定标题
     * @param string $html
     * @return string
     */
    public function exchange_title_all(string $title_fixed, string $html, array $key_array): string
    {

        // 出现了几次
        $title_file = @file_get_contents(storage_path("app/public/template/$this->template/key/t.txt"));
        if (!$title_file) {
            die("<h2 style='text-align: center'> t.txt </h2>");
        }
        $title_array = explode("\n", trim(str_replace("\r", '', $title_file)));
        $title_array_count = count($title_array);
        if (!$title_array_count) {
            die("<h2 style='text-align: center'> t.txt 没数据 </h2>");
        }
        return str_replace("{固定标题}", $title_array[rand(0, $title_array_count - 1)], $html);
    }

    /**
     * 替换key
     * @param string $html
     * @return string
     */
    public function exchange_key_all(string $html): string
    {
        // 出现了几次
        $title_str_count = substr_count($html, '{随机关键词}');
        $title_file = @file_get_contents(storage_path("app/public/template/$this->template/key/k.txt"));
        if (!$title_file) {
            die("<h2 style='text-align: center'> k.txt </h2>");
        }
        $title_array = explode("\n", trim(str_replace("\r", '', $title_file)));
        $title_array_count = count($title_array);
        if (!$title_array_count) {
            die("<h2 style='text-align: center'> t.txt 没数据 </h2>");
        }
        $title_fixed = $title_array[rand(0, $title_array_count - 1)];
        // 替换关键词
        $html = str_replace("{固定关键词}", $title_fixed, $html);
        $html = $this->exchange_title_all($title_fixed, $html, $title_array);
        $html = $this->exchange_description_all($title_fixed, $html);
        // 有几个替换几个
        for ($i = 0; $i < $title_str_count; $i++) {
            $html = preg_replace("/{随机关键词}/", $title_array[rand(0, $title_array_count - 1)], $html, 1);
        }
        return str_replace("{固定关键词}", $title_fixed, $html);
    }

    /**
     * 随机描述
     * @param string $html
     * @return string
     */
    public function exchange_description_all(string $key, string $html): string
    {
        // 出现了几次
        $description_str_count = substr_count($html, '{随机描述}');
        $description_file = @file_get_contents(storage_path("app/public/template/$this->template/key/d.txt"));
        if (!$description_file) {
            die("<h2 style='text-align: center'> d.txt </h2>");
        }
        $description_array = explode("\n", trim(str_replace("\r", '', $description_file)));
        $description_array_count = count($description_array);
        if (!$description_array_count) {
            die("<h2 style='text-align: center'> d.txt 没数据 </h2>");
        }
        // 有几个替换几个
        for ($i = 0; $i < $description_str_count; $i++) {
            $html = preg_replace("/{随机描述}/", $description_array[rand(0, $description_array_count - 1)], $html, 1);
        }
        return str_replace("{固定关键词}", $key, $html);
    }


    /**
     * 随机替换图片
     * @param string $html
     * @return string
     */
    public function exchange_img(string $html): string
    {
        // 出现了几次
        $img_array = glob(storage_path("app/public/template/$this->template/img") . '/*.{jpg,pdf,png,jpeg}', GLOB_BRACE);
        $img_fixed = explode('/public', $img_array[rand(0, count($img_array) - 1)])[1];
        $html = str_replace("{固定图片}", '/storage' . $img_fixed, $html);
        $img_str_count = substr_count($html, '{随机图片}');
        // 有几个替换几个
        for ($i = 0; $i < $img_str_count; $i++) {
            $html = preg_replace("/{随机图片}/", '/storage' . explode('/public', $img_array[rand(0, count($img_array) - 1)])[1], $html, 1);
        }
        return $html;
    }

    /**
     * 随机列表链接
     * @param string $html
     * @return string
     */
    public function exchange_list_link(string $html): string
    {
        $img_str_count = substr_count($html, '{随机列表链接}');
        $prefix_path = '';
        // 有几个替换几个
        for ($i = 0; $i < $img_str_count; $i++) {
            $prefix_str = '';
            // 泛前缀
            if ($this->prefix_status) {
                $prefix_str = array_rand($this->prefix_array) . '.';
            }
            // 泛目录
            if ($this->prefix_path_status) {
                $prefix_path = '/' . $this->request_url_array[rand(0, count($this->request_url_array) - 1)] . '.html';
            }
            $html = preg_replace("/{随机列表链接}/", '//' . $prefix_str . $this->host . $prefix_path, $html, 1);
        }
        return $html;
    }

    /**
     * 随机泛域名
     * @param string $html
     * @return string
     */
    public function exchange_link(string $html): string
    {
        $link_count = substr_count($html, '{随机泛域名}');

        // 有几个替换几个
        for ($i = 0; $i < $link_count; $i++) {
            $prefix_str = array_rand($this->prefix_array) . '.';
            $html = preg_replace("/{随机泛域名}/", '//' . $prefix_str . $this->host, $html, 1);
        }
        return $html;
    }

    /**
     * 随机详情链接
     * @param string $html
     * @return string
     */
    public function exchange_row_link(string $html): string
    {
        $img_str_count = substr_count($html, '{随机详情链接}');
        $prefix_path = '';
        // 有几个替换几个
        for ($i = 0; $i < $img_str_count; $i++) {
            $prefix_str = '';
            // 泛前缀
            if ($this->prefix_status) {
                $prefix_str = array_rand($this->prefix_array) . '.';
            }
            // 泛目录
            if ($this->prefix_path_status) {
                $prefix_path = '/' . $this->request_url_array[rand(0, count($this->request_url_array) - 1)] . $this->request_url_array[rand(0, count($this->request_url_array) - 1)] . '/' . rand(0, 999999) . '.html';
            }

            $html = preg_replace("/{随机详情链接}/", '//' . $prefix_str . $this->host . $prefix_path, $html, 1);
        }
        return $html;
    }

    /**
     * 随机数字
     * @param string $html
     * @return string
     */
    public function exchange_number(string $html): string
    {
        $number_count = substr_count($html, '{随机数字}');
        for ($i = 0; $i < $number_count; $i++) {
            $html = preg_replace("/{随机数字}/", rand(0, 99999), $html, 1);
        }
        return $html;
    }

    /**
     * 随机小数点
     * @param string $html
     * @return string
     */
    public function exchange_number_n(string $html): string
    {
        $number_count = substr_count($html, '{随机小数点}');
        for ($i = 0; $i < $number_count; $i++) {
            $html = preg_replace("/{随机小数点}/", rand(0, 999) . '.' . rand(0, 99), $html, 1);
        }
        return $html;
    }

    /**
     * 替换时间
     * @param string $html
     * @return string
     */
    public function exchange_date(string $html): string
    {
        $number_count = substr_count($html, '{随机时间}');
        for ($i = 0; $i < $number_count; $i++) {
            $html = preg_replace("/{随机时间}/", $this->random_date(20210420, date('Ymd')), $html, 1);
        }
        return $html;
    }

    public function random_date($begin_time, $end_time = "", $is = true)
    {
        $begin = strtotime($begin_time);
        $end = $end_time == "" ? mktime() : strtotime($end_time);
        $timestamp = rand($begin, $end);
        return $is ? date("Y-m-d H:i", $timestamp) : $timestamp;
    }

    /**
     * 随机网名
     * @param string $html
     * @return string
     */
    public function exchange_nickname(string $html): string
    {
        $number_count = substr_count($html, '{随机网名}');
        for ($i = 0; $i < $number_count; $i++) {
            $html = preg_replace("/{随机网名}/", $this->nickname[rand(0, count($this->nickname) - 1)], $html, 1);
        }
        return $html;
    }

    /**
     * 随机句子
     * @param string $html
     * @return string
     */
    public function exchange_content(string $html): string
    {
        // 出现了几次
        $content_str_count = substr_count($html, '{随机句子}');
        $content_file = @file_get_contents(storage_path("app/public/template/$this->template/key/c.txt"));
        if (!$content_file) {
            die("<h2 style='text-align: center'> c.txt </h2>");
        }
        $content_array = explode("\n", trim(str_replace("\r", '', $content_file)));
        $content_array_count = count($content_array);
        if (!$content_array_count) {
            die("<h2 style='text-align: center'> c.txt 没数据 </h2>");
        }
        // 有几个替换几个
        for ($i = 0; $i < $content_str_count; $i++) {
            $html = preg_replace("/{随机句子}/", $content_array[rand(0, $content_array_count - 1)], $html, 1);
        }
        return $html;
    }

    public function put_file_html(string $path, string $html)
    {
        Storage::disk('local')->put($path, $html);
    }

    /**
     * @throws FileNotFoundException
     */
    public function get_file_html(string $path): string
    {
        if (Storage::disk('local')->exists($path)) {
            return @Storage::disk('local')->get($path);
        }
        return false;
    }

    public function spider(string $useragent, $url, $ip)
    {
        if (stripos($useragent, 'googlebot') !== false) {
            $bot = 'Google Spider';
            $this->bot_spider = false;
        } elseif (stripos($useragent, 'baiduspider') !== false) {
            $bot = 'Baidu Spider';
        } elseif (stripos($useragent, 'sogou spider') !== false) {
            $bot = 'Sogou Spider';
        } elseif (stripos($useragent, 'sosospider') !== false) {
            $bot = 'SOSO Spider';
        } elseif (stripos($useragent, '360spider') !== false) {
            $bot = '360 Spider';
        } elseif (stripos($useragent, 'yahoo') !== false) {
            $bot = 'Yahoo Spider';
        } elseif (stripos($useragent, 'msn') !== false) {
            $bot = 'MSN Spider';
        } elseif (stripos($useragent, 'sohu') !== false) {
            $bot = 'Sohu Spider';
        } elseif (stripos($useragent, 'yodaoBot') !== false) {
            $bot = 'Yodao Spider';
        } else {
            $bot = 'NO Spider';
        }
        (new \Monolog\Logger('local'))
            ->pushHandler(new RotatingFileHandler(storage_path("logs/$this->host/spider.log")))
            ->info("$url|$this->host|$bot|$ip|$useragent");
        // 蜘蛛模式
        if ($bot !== 'NO Spider') {
            $this->bot_spider = true;
        }
    }

    /**
     * 百度推送
     * @return void
     */
    public function is_jump(array $model): bool
    {

    }

}
