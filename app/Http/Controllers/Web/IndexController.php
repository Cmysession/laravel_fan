<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{

    public function index()
    {
        $tmp = 'm_1';
        $index_html = file_get_contents(storage_path("emp/{$tmp}/index.html"));
        die($index_html);
    }

    public function list(Request $request)
    {
        die('list');
    }

    public function row(Request $request)
    {
        die('row');
    }
}
