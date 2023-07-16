<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public $template = 'm_1';

    public $title_str = "{标题}";


    public function __construct()
    {

    }


    public function index(): void
    {
        $index_html = @file_get_contents(storage_path("template/$this->template/index.html"));
        if (!$index_html) {
            die("<h2 style='text-align: center'> index.html </h2>");
        }
        die($this->exchange($index_html));
    }

    public function list(): void
    {

        $index_html = @file_get_contents(storage_path("template/$this->template/list.html"));
        if (!$index_html) {
            die("<h2 style='text-align: center'> list.html </h2>");
        }
        die($index_html);
    }

    public function row(): void
    {

        $index_html = @file_get_contents(storage_path("template/$this->template/row.html"));
        if (!$index_html) {
            die("<h2 style='text-align: center'> row.html </h2>");
        }
        die($index_html);
    }

    public function exchange(string $html): string
    {
        // 出现了几次
        $title_str_count = substr_count($html, $this->title_str);
        $title_file = @file_get_contents(storage_path("template/$this->template/key/t.txt"));
        if (!$title_file) {
            die("<h2 style='text-align: center'> t.txt </h2>");
        }
        $title_array = explode("\n", $title_file);
        $title_array_count = count($title_array);
        if (!$title_array_count) {
            die("<h2 style='text-align: center'> t.txt 没数据 </h2>");
        }
        // 有几个替换几个
        for ($i = 0; $i < $title_str_count; $i++) {
            $html = preg_replace("/{$this->title_str}/", $title_array[rand(0, $title_array_count - 1)], $html, 1);
        }
        return $html;
    }

}
