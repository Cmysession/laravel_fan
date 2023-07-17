<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public $template = null;

    /**
     * "{固定关键词}"
     * "{随机关键词}"
     * "{随机关键词}"
     */

    public function __construct()
    {
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
        die($index_html);
    }

    public function exchange(string $html): string
    {
        $html = $this->exchange_title_all($html);
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


}
