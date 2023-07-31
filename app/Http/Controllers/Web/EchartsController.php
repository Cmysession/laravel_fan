<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EchartsController extends Controller
{
    public function index()
    {
        return view('echarts.index');
    }

    /**
     * 获取所有的域名
     * @return void
     */
    public function get_all_host()
    {

        $handler = opendir(storage_path('logs'));
        $files = [];
        while (($filename = readdir($handler)) !== false) {
            // 务必使用!==，防止目录下出现类似文件名“0”等情况
            if ($filename !== "." && $filename !== ".." && $filename !== ".gitignore") {
                $files[] = ['host' => $filename];
            }
        }
        closedir($handler);
        return json_encode($files);
    }

    /**
     * 获取走势图数据
     * @return void
     */
    public function get_charts_data(Request $request)
    {
        ini_set('date.timezone', 'Asia/Shanghai');
        set_time_limit(0);
        $host_date_array = [];
        $handler = opendir(storage_path('logs/' . $request->post('host')));
        while (($filename = readdir($handler)) !== false) {
            // 务必使用!==，防止目录下出现类似文件名“0”等情况
            if ($filename !== "." && $filename !== ".." && $filename !== ".gitignore") {
                // 日期
                $host_date_array[] = $filename;
            }
        }
        closedir($handler);
        $host_date_log_array = [];
        for ($i = 0; $i < count($host_date_array); $i++) {
            $file = fopen(storage_path("logs/{$request->post('host')}/{$host_date_array[$i]}"), "r");
//            //检测指正是否到达文件的未端
            while (!feof($file)) {
                $line = fgets($file);
                if ($line) {
                    // 处理蜘蛛
                    $line_exp = explode('|', $line);
                    $line_exp_one = explode(' local.INFO: ', $line_exp[0]);
                    // 时间为KEY
//                    $host_date_log_array[
//                        date('Y-m-d', strtotime(substr($line_exp_one[0], 1, -1)))
//                    ][] = [
//                        'data' => date('Y-m-d H:i:s', strtotime(substr($line_exp_one[0], 1, -1))),
//                        'url' => $line_exp_one[1],
//                        'ip' => $line_exp[3],
//                    ];
                    $host_date_array = [
                        [
                            "name" => "百度",
                            "type" => "line",
                            "stack" => "Total",
                            "data" => [],
                        ],
                    ];
                }
            }
            fclose($file);
        }
        dump($host_date_log_array);
    }

    public function get_table_data(Request $request)
    {
        
    }
}
