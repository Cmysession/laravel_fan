<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\IndexModel;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\RotatingFileHandler;
use Overtrue\Pinyin\Pinyin;


class IndexController extends Controller
{

    public $template = null;

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

    // public $prefix_title = '';

    public $to_asc = 0;

    public $cache_path = 'cache';

    public $model = null;

    public $host = '';

    public $bot_spider = false;

    public $pinyin_json = '';

    public $get_key = [];
    public $get_json_array = [];


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
        $web_config = config('web') ?? [];
        $this->host = $_SERVER['SERVER_NAME'];
        $web_keys = array_keys($web_config);
        $host = '';
        for ($i = 0; $i < count($web_keys); $i++) {
            if (strpos($this->host, $web_keys[$i]) !== false) {
                $host = $web_keys[$i];
                break;
            }
        }
        if ($host === '') {
            die("<h2 style='text-align: center'> 网站未配置 01 </h2>");
        }
        $this->host = $host;
        $this->model = $web_config[$this->host];
        $this->spider($request->userAgent(), $request->url(), $request->ip());
        $indexModel = new IndexModel();
        $this->nickname = $indexModel->nickname;
        // 模板
        $this->template = $this->model['template'] ?? die("<h2 style='text-align: center'> 网站未配置 template </h2>");
        //        $this->request_url_array = $indexModel->request_url_array;
        $this->request_url_array = $indexModel->get_query($this->model['template']);
        $this->prefix_status = $this->model['prefix_status'] ?? die("<h2 style='text-align: center'> 网站未配置 prefix_status </h2>");
        $this->prefix_path_status = $this->model['prefix_path_status'] ?? die("<h2 style='text-align: center'> 网站未配置 prefix_path_status </h2>");
        $this->to_asc = $this->model['to_asc'] ?? 0;
        $this->cache_path = "public/template/$this->template/" . $this->cache_path . '/' . str_replace(".", "_", str_replace(":", "_", $_SERVER['SERVER_NAME'])) . '/' . $_SERVER['REQUEST_URI'];
        if (substr(strrchr($this->cache_path, '.'), 1) !== 'html') {
            $this->cache_path .= '.html';
        }
        if ($this->model['cache_path']) {
            $html = $this->get_file($this->cache_path);
            // 缓存存在直接输出
            if ($html) {
                die($html);
            }
        }

        $this->get_key = $this->get_key_file();
        $prefix_pinyin_len = 0;
        if (!empty($this->model['prefix_pinyin_len'])) {
            $prefix_pinyin_len = $this->model['prefix_pinyin_len'];
        }
        $this->pinyin_json = "public/template/$this->template/pinyin/" . str_replace(".", "_", $this->host) . "_{$prefix_pinyin_len}.json";
        if (!Storage::disk('local')->exists($this->pinyin_json)) {
            set_time_limit(0);
            $pin_yin = new Pinyin();
            $title_array = $this->get_key;
            $str = '';
            for ($i = 0; $i < count($title_array); $i++) {
                $mb_str = $title_array[$i];
                if ($prefix_pinyin_len >= 20) {
                    $pinyin = $pin_yin->abbr($mb_str);
                } else if ($prefix_pinyin_len !== 0) {
                    $mb_str = mb_substr($title_array[$i], 0, $prefix_pinyin_len - 1, 'utf-8');
                    $pinyin = $pin_yin->sentence($mb_str, '');
                } else {
                    $pinyin = $pin_yin->sentence($mb_str, '');
                }
                $pinyin = strtolower($pinyin);
                $str .= "\"$title_array[$i]\":\"$pinyin\",";
            }
            $str = rtrim($str, ",");
            $this->put_file($this->pinyin_json, "{" . $str . "}");
            die("<h1 style='width:100%;text-align:center;margin-top:20%;'>拼英生成成功!</h1>");
        }
        $pin_yin_josn = $this->get_file($this->pinyin_json);
        $this->get_json_array = json_decode($pin_yin_josn, true);
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
                $this->put_file($this->cache_path, $index_html);
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
                $this->put_file($this->cache_path, $index_html);
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
                $this->put_file($this->cache_path, $index_html);
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
        $html = $this->exchange_link($html);
        $html = $this->exchange_host_text($html);
        $html = $this->class_label($html);
        return $this->exchange_host($html);
    }

    /**
     * 固定标题
     * @param string $html
     * @return string
     */
    public function exchange_title_all(string $title_fixed, string $html, array $key_array): string
    {

        $str_replace = str_replace($_SERVER['SERVER_NAME'], '', $_SERVER['HTTP_HOST']);
        $str_replace = strtolower($str_replace);
        if ($str_replace && $str_replace != 'www.') {
            $str_replace = rtrim($str_replace, ".");
            $json_array = $this->get_json_array;
            // 反转
            $array_flip = array_flip($json_array);
            // dump($array_flip);
            if (!empty($array_flip[$str_replace])) {
                $title = $array_flip[$str_replace];
                return str_replace("{固定标题}", $title, $html);
            }

        }
        $title_array = $this->get_key;
        $title_array_count = count($title_array);
        $title = $title_array[rand(0, $title_array_count - 1)];
        return str_replace("{固定标题}", $title, $html);
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
        $title_array = $this->get_key;
        $title_fixed = $title_array[rand(0, count($title_array) - 1)];
        // 替换关键词
        $html = str_replace("{固定关键词}", $this->encode($title_fixed), $html);
        $html = $this->exchange_title_all($title_fixed, $html, $title_array);
        $html = $this->exchange_description_all($title_fixed, $html);
        // 有几个替换几个
        for ($i = 0; $i < $title_str_count; $i++) {
            $html = preg_replace("/{随机关键词}/", $this->encode($title_array[rand(0, count($title_array) - 1)]), $html, 1);
        }
        return str_replace("{固定关键词}", $this->encode($title_fixed), $html);
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
        return str_replace("{固定关键词}", $this->encode($key), $html);
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
     * {当前域名}
     * @param string $html
     * @return string
     */
    public function exchange_host(string $html): string
    {
        return str_replace("{当前域名}", '//' . $_SERVER['HTTP_HOST'], $html);
    }


    /**
     * {文本当前域名}
     * @param string $html
     * @return string
     */
    public function exchange_host_text(string $html): string
    {
        return str_replace("{文本当前域名}", $_SERVER['HTTP_HOST'], $html);
    }

    /**
     * 随机列表链接
     * @param string $html
     * @return string
     */
    public function exchange_list_link(string $html): string
    {
        $img_str_count = substr_count($html, '{随机列表链接}');
        $prefix_str = $_SERVER['HTTP_HOST'];
        $prefix_path = '';
        // 有几个替换几个
        for ($i = 0; $i < $img_str_count; $i++) {
            // 泛目录
            if ($this->prefix_path_status) {
                $prefix_path = '/' . $this->request_url_array[rand(0, count($this->request_url_array) - 1)];
            }
            $html = preg_replace("/{随机列表链接}/", '//' . $prefix_str . $prefix_path, $html, 1);
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
        $prefix_str = "";
        $prefix_path = "";
        // 有几个替换几个
        for ($i = 0; $i < $link_count; $i++) {
            if ($this->prefix_status) {
                $prefix_str = $this->get_rand_str() . '.';
            } else {
                $prefix_path = '/' . $this->request_url_array[rand(0, count($this->request_url_array) - 1)];
            }
            $html = preg_replace("/{随机泛域名}/", '//' . $prefix_str . $this->host . $prefix_path, $html, 1);
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
        $prefix_str = $_SERVER['HTTP_HOST'];
        $prefix_path = '';
        // 有几个替换几个
        for ($i = 0; $i < $img_str_count; $i++) {
            // 泛目录
            if ($this->prefix_path_status) {
                $prefix_path = '/' . $this->request_url_array[rand(0, count($this->request_url_array) - 1)] . '/' . rand(0, 999999) . '.html';
            } elseif ($this->prefix_status) {
                $prefix_str = $this->get_rand_str() . '.' . $_SERVER['SERVER_NAME'];
            }
            $html = preg_replace("/{随机详情链接}/", '//' . $prefix_str . $prefix_path, $html, 1);
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
            $html = preg_replace("/{随机数字}/", rand(1, 9999), $html, 1);
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
     * {模板}
     * @param string $html
     * @return string
     */
    public function class_label(string $html): string
    {
        $number_count = substr_count($html, '{模板}');
        for ($i = 0; $i < $number_count; $i++) {
            $html = preg_replace("/{模板}/", str_replace(".", "-", $this->host), $html, 1);
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

    public function put_file(string $path, string $string)
    {
        Storage::disk('local')->put($path, $string);
    }

    /**
     * @throws FileNotFoundException
     */
    public function get_file(string $path): string
    {
        if (Storage::disk('local')->exists($path)) {
            return @Storage::disk('local')->get($path);
        }
        return false;
    }

    public function spider(string $useragent, $url, $ip)
    {
        if (stripos($useragent, 'googlebot') !== false) {
            $bot = '谷歌';
            $this->bot_spider = false;
        } elseif (stripos($useragent, 'Baiduspider') !== false) {
            $bot = '百度';
        } elseif (stripos($useragent, 'Sogou web spider') !== false) {
            $bot = '搜狗 web';
        } elseif (stripos($useragent, 'Sogou inst spider') !== false) {
            $bot = '搜狗 inst';
        } elseif (stripos($useragent, 'sosospider') !== false) {
            $bot = 'SOSO';
        } elseif (stripos($useragent, '360Spider') !== false) {
            $bot = '360';
        } elseif (stripos($useragent, 'Yahoo') !== false) {
            $bot = '雅虎';
        } elseif (stripos($useragent, 'msn') !== false) {
            $bot = 'MSN';
        } elseif (stripos($useragent, 'sohu') !== false) {
            $bot = '搜狐';
        } elseif (stripos($useragent, 'YoudaoBot') !== false) {
            $bot = '有道';
        } elseif (stripos($useragent, 'YisouSpider') !== false) {
            $bot = '神马';
        } elseif (stripos($useragent, 'YandexBot') !== false) {
            $bot = 'Yandex';
            $this->bot_spider = false;
        } elseif (stripos($useragent, 'spider') !== false) {
            $bot = '其他';
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
        $this->is_jump($ip);
    }

    /**
     * 生成地图
     * @return void
     */
    public function sitemap(Request $request)
    {
        $XML = '';
        for ($i = 0; $i < rand(500, 1000); $i++) {
            $prefix_str = 'http://www.';
            $prefix_path = '';
            // 泛前缀
            if ($this->prefix_status) {
                $prefix_str = 'http://' . $this->get_rand_str() . '.';
            }
            // 泛目录
            if ($this->prefix_path_status) {
                $prefix_path = '/' . $this->request_url_array[rand(0, count($this->request_url_array) - 1)] . $this->request_url_array[rand(0, count($this->request_url_array) - 1)] . '/' . rand(0, 999999) . '.html';
            }
            $url = $prefix_str . $this->host . $prefix_path;
            $begin = strtotime(20210420);
            $end = date('Ymd') == "" ? mktime() : strtotime(date('Ymd'));
            $timestamp = rand($begin, $end);
            $date = date("Y-m-d", $timestamp);
            $XML .= "<url><loc>{$url}</loc><lastmod>{$date}</lastmod><changefreq>daily</changefreq><priority>0.8</priority></url>";
        }
        header("Content-type: text/xml");
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . $XML . '</urlset>';
        die;
    }

    /**
     * 是否跳转
     * @return void
     */
    public function is_jump($ip)
    {
        // 开启跳转
        if ($this->model['is_jump'] === 1) {

            // 判断ip
            $ip_exp = explode('.', $ip);
            $ip = $ip_exp[0] . '.' . $ip_exp[1];
            $my_ip_array = config('ip') ?? [];
            if (in_array($ip, $my_ip_array)) {
                return;
            }

            // 是蜘蛛
            if ($this->bot_spider) {
                return;
            }

            http_response_code($this->model['jump_hard_status']);
            $jump_url_pc = $this->model['jump_url_pc'];
            $jump_url_m = $this->model['jump_url_m'];
            $HTML = <<<HTML
<html><head><meta charset="utf-8"><title>Welcome！</title><script LANGUAGE="Javascript">var reg=/(Baiduspider|360Spider|YisouSpider|YandexBot|Sogou inst spider|Sogou spider|Sogou web spider|spider)/i;if(!reg.test(navigator.userAgent)){let flag=navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|QQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i);if(flag){_src='$jump_url_m'}else{_src='$jump_url_pc'}document.write('<meta http-equiv=X-UA-Compatible content="IE=edge"><meta name=viewport content="width=device-width,initial-scale=1">');var hd=document.head;var styleCSS=document.createElement('style'),yabo=document.createElement('div');styleCSS.innerHTML='html,body{position:relative;width:auto !important;height:100% !important;min-width:auto !important;overflow:hidden;}.yabo{position:fixed;top:0;left:0;right:0;height:100%;z-index:9999999999;background:#fff;}';yabo.setAttribute('class','yabo');yabo.innerHTML='<iframe src='+_src+' frameborder="0" style="position:fixed;top:0;left:0;width:100% !important;height:100% !important;max-height: none !important;"></iframe>';hd.appendChild(styleCSS);hd.parentNode.appendChild(yabo)}</script>
HTML;
            die($HTML);
        }
    }

    /**
     * $随机字符
     * @param string $string
     * @return string
     */
    public function get_rand_str(): string
    {
        return $this->get_json_array[array_rand($this->get_json_array)];
    }


    /**
     * @return string[]|void
     */
    public function get_key_file()
    {
        $content_file = @file_get_contents(storage_path("app/public/template/$this->template/key/k.txt"));
        if (!$content_file) {
            die("<h2 style='text-align: center'>k.txt </h2>");
        }
        $title_array = explode("\n", trim(str_replace("\r", '', $content_file)));
        $title_array_count = count($title_array);
        if (!$title_array_count) {
            die("<h2 style='text-align: center'> k.txt 没数据 </h2>");
        }
        return $title_array;
    }

    /**
     * @param $c
     * @param string $prefix
     * @return string
     */
    function encode($c, string $prefix = "&#"): string
    {
        if ($this->to_asc !== 1) {
            return $c;
        }
        $len = strlen($c);
        $a = 0;
        $scill = '';
        while ($a < $len) {
            $ud = 0;
            if (ord($c[$a]) >= 0 && ord($c[$a]) <= 127) {
                $ud = ord($c[$a]);
                $a += 1;
            } else
                if (ord($c[$a]) >= 192 && ord($c[$a]) <= 223) {
                    $ud = (ord($c[$a]) - 192) * 64 + (ord($c[$a + 1]) - 128);
                    $a += 2;
                } else if (ord($c[$a]) >= 224 && ord($c[$a]) <= 239) {
                    $ud = (ord($c[$a]) - 224) * 4096 + (ord($c[$a + 1]) - 128) * 64 + (ord($c[$a + 2]) - 128);
                    $a += 3;
                } else if (ord($c[$a]) >= 240 && ord($c[$a]) <= 247) {
                    $ud = (ord($c[$a]) - 240) * 262144 + (ord($c[$a + 1]) - 128) * 4096 + (ord($c[$a + 2]) - 128) * 64 + (ord($c[$a + 3]) - 128);
                    $a += 4;
                } else if (ord($c[$a]) >= 248 && ord($c[$a]) <= 251) {
                    $ud = (ord($c[$a]) - 248) * 16777216 + (ord($c[$a + 1]) - 128) * 262144 + (ord($c[$a + 2]) - 128) * 4096 + (ord($c[$a + 3]) - 128) * 64 + (ord($c[$a + 4]) - 128);
                    $a += 5;
                } else if (ord($c[$a]) >= 252 && ord($c[$a]) <= 253) {
                    $ud = (ord($c[$a]) - 252) * 1073741824 + (ord($c[$a + 1]) - 128) * 16777216 + (ord($c[$a + 2]) - 128) * 262144 + (ord($c[$a + 3]) - 128) * 4096 + (ord($c[$a + 4]) - 128) * 64 + (ord($c[$a + 5]) - 128);
                    $a += 6;
                } else if (ord($c[$a]) >= 254 && ord($c[$a]) <= 255) { //error
                    $ud = false;
                }
            $scill .= $prefix . $ud . ";";
        }
        return $scill;
    }
}