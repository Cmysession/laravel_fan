<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public $template = null;

    /**
     * 泛域名前缀
     * @var string[]
     */
    public $prefix_array = [
        "shanghai" => "上海",
        "yunnan" => "云南",
        "inner_mongolia" => "内蒙古",
        "beijing" => "北京",
        "taiwan" => "台湾",
        "jilin" => "吉林",
        "sichuan" => "四川",
        "tianjin" => "天津",
        "ningxia" => "宁夏",
        "anhui" => "安徽",
        "shandong" => "山东",
        "shanxi" => "山西",
        "guangdong" => "广东",
        "guangxi" => "广西",
        "xinjiang" => "新疆",
        "jiangsu" => "江苏",
        "jiangxi" => "江西",
        "hebei" => "河北",
        "henan" => "河南",
        "zhejiang" => "浙江",
        "hainan" => "海南",
        "hubei" => "湖北",
        "hunan" => "湖南",
        "macao" => "澳门",
        "gansu" => "甘肃",
        "fujian" => "福建",
        "tibet" => "西藏",
        "guizhou" => "贵州",
        "liaoning" => "辽宁",
        "chongqing" => "重庆",
        "shaanxi" => "陕西",
        "qinhai" => "青海",
        "hong_kong" => "香港",
        "heilongjiang" => "黑龙江",
    ];

    public $prefix_title = '';

    /**
     * 种类|类别
     * @var string[]
     */
    public $request_url_array = ['news', 'props', 'commodity', 'mechanical'];

    /**
     * 是否开启泛域名
     * @var int
     */
    public $prefix_status = 1;

    /**
     * yzy345.com
     * "{固定关键词}"
     * "{随机关键词}"
     * "{随机图片}"
     * "{固定图片}"
     * "{随机列表链接}"
     * "{随机详情链接}"
     */

    public function __construct()
    {
        $prefix_title = mb_substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], '.'));
        if ($prefix_title) {
            if (!empty($this->prefix_array[$prefix_title])) {
                $this->prefix_title = $this->prefix_array[$prefix_title];
            }
        }
        // 获取域名
        $this->template = 'm_1';
    }

    /**
     * 主页
     * @return void
     */
    public function index(): void
    {
        $index_html = @file_get_contents(storage_path("app/public/template/$this->template/index.html"));
        if (!$index_html) {
            die("<h2 style='text-align: center'> index.html </h2>");
        }
        die($this->exchange($index_html));
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
        die($this->exchange($index_html));
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
        die($this->exchange($index_html));
    }

    public function exchange(string $html): string
    {
        $html = $this->exchange_title_all($html);
        $html = $this->exchange_list_link($html);
        $html = $this->exchange_row_link($html);
        return $this->exchange_img($html);
    }

    /**
     * 随机替换关键词
     * @param string $html
     * @return string
     */
    public function exchange_title_all(string $html): string
    {
        // 出现了几次
        $title_str_count = substr_count($html, '{随机关键词}');
        $title_file = @file_get_contents(storage_path("app/public/template/$this->template/key/t.txt"));
        if (!$title_file) {
            die("<h2 style='text-align: center'> t.txt </h2>");
        }
        $title_array = explode("\n", trim($title_file));
        $title_array_count = count($title_array);
        if (!$title_array_count) {
            die("<h2 style='text-align: center'> t.txt 没数据 </h2>");
        }
        $title_fixed = $title_array[rand(0, $title_array_count - 1)];
        // 替换首页title
        $body_title = $title_fixed . '(中国' . ($this->prefix_title === '' ? '' : '·' . $this->prefix_title) . ')有限公司';
        $html = preg_replace("/{随机标题}/", $body_title, $html, 1);
        // 替换关键词
        $html = str_replace("{固定关键词}", $title_fixed, $html);
        // 有几个替换几个
        for ($i = 0; $i < $title_str_count; $i++) {
            $html = preg_replace("/{随机关键词}/", $title_array[rand(0, $title_array_count - 1)], $html, 1);
        }
        return $html;
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
            $html = preg_replace("/{随机图片}/", '/storage' . explode('/public', $img_array[rand(0, $img_str_count - 1)])[1], $html, 1);
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
        // 有几个替换几个
        for ($i = 0; $i < $img_str_count; $i++) {
            $prefix_str = '';
            // 泛前缀
            if ($this->prefix_status) {
                $prefix_str = array_rand($this->prefix_array);
            }
            $html = preg_replace("/{随机列表链接}/", '//' . $prefix_str . '.' . $_SERVER['SERVER_NAME'] . '/' . $this->request_url_array[rand(0, count($this->request_url_array) - 1)] . '.html', $html, 1);
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
        // 有几个替换几个
        for ($i = 0; $i < $img_str_count; $i++) {
            $prefix_str = '';
            // 泛前缀
            if ($this->prefix_status) {
                $prefix_str = array_rand($this->prefix_array);
            }
            $html = preg_replace("/{随机详情链接}/", '//' . $prefix_str . '.' . $_SERVER['SERVER_NAME'] . '/' . $this->request_url_array[rand(0, count($this->request_url_array) - 1)] . '/' . rand(0, 999999) . '.html', $html, 1);
        }
        return $html;
    }
}
