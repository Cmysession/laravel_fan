<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public $template = 'm_1';


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

        $title_str = "{标题}";
        // 出现了几次
        $title_str_count = substr_count($html, $title_str);
        $title_file = @file_get_contents(storage_path("template/$this->template/key/t.txt"));
        if (!$title_file){
            die("<h2 style='text-align: center'> t.txt</h2>");
        }
        dump($title_file);
        // 替换
        for ($i = 0; $i < $title_str_count; $i++) {
            $html = preg_replace("/$title_str/", '34', $html, 1);
        }
//        var_dump($html);
        return $html;
    }

}
