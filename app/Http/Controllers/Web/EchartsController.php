<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return json_encode($this->get_all_host_fun());
    }

    /**
     * 获取走势图数据
     * @return void
     */
    public function get_charts_data(Request $request)
    {
        ini_set('date.timezone', 'Asia/Shanghai');
        set_time_limit(0);
        $host_date_log_array = [
            'selected_array' => ['百度', "360", '神马', '搜狗'],
            'selected' => ['百度' => true, "360" => true, '神马' => true, '搜狗' => true],
            "xAxis_data" => [],
            'series' => [
                [
                    'name' => '百度',
                    'type' => 'line',
//                    'stack' => 'Total'
                ],
                [
                    'name' => "360",
                    'type' => 'line',
//                    'stack' => 'Total'
                ],
                [
                    'name' => '神马',
                    'type' => 'line',
//                    'stack' => 'Total'
                ],
                [
                    'name' => '搜狗',
                    'type' => 'line',
//                    'stack' => 'Total'
                ]
            ],
        ];
        //Baiduspider  百度
        //360Spider  360
        //YisouSpider 神马
        //Sogou inst spider | Sogou web spider | Sogou spider
        $day = 90;
        $date = date('Y-m-d', strtotime("-$day day"));;
        // 查询30天内的数据
        for ($i = 0; $i < $day + 1; $i++) {
            $date_name = "spider-{$date}.log";
            $file_path = storage_path("logs/{$request->post('host')}/$date_name");
            $host_date_log_array['xAxis_data'][] = $date; // 日期
            if (file_exists($file_path)) {
                $file_get_contents = file_get_contents($file_path);
                $host_date_log_array['series'][0]['data'][] = substr_count($file_get_contents, 'Baiduspider');
                $host_date_log_array['series'][1]['data'][] = substr_count($file_get_contents, '360Spider');
                $host_date_log_array['series'][2]['data'][] = substr_count($file_get_contents, 'YisouSpider');
                $host_date_log_array['series'][3]['data'][] = substr_count($file_get_contents, 'Sogou inst spider')
                    + substr_count($file_get_contents, 'Sogou web spider')
                    + substr_count($file_get_contents, 'Sogou spider');
            } else {
                $host_date_log_array['series'][0]['data'][] = 0;
                $host_date_log_array['series'][1]['data'][] = 0;
                $host_date_log_array['series'][2]['data'][] = 0;
                $host_date_log_array['series'][3]['data'][] = 0;
            }
            $date = date('Y-m-d', strtotime("+1 day", strtotime($date)));
        }
        return json_encode($host_date_log_array);
    }

    /**
     * 获取所有域名每日的
     * @param Request $request
     * @return void
     */
    public function get_day_charts(Request $request)
    {
        ini_set('date.timezone', 'Asia/Shanghai');
        set_time_limit(0);
        $host_array = $this->get_all_host_fun();

        $host_date_log_array = [
            'legend_data' => ['百度', "360", '神马', '搜狗'],
            "xAxis_data" => [],//域名
            'series' => [
                [
                    'name' => '百度',
                    'type' => 'bar',
                    'barGap' => 0,
                    'label' => 'labelOption',
                    'emphasis' => ['focus' => 'series'],
                    'data' => [],
                ],
                [
                    'name' => '360',
                    'type' => 'bar',
                    'barGap' => 0,
                    'label' => 'labelOption',
                    'emphasis' => ['focus' => 'series'],
                    'data' => [],
                ],
                [
                    'name' => '神马',
                    'type' => 'bar',
                    'barGap' => 0,
                    'label' => 'labelOption',
                    'emphasis' => ['focus' => 'series'],
                    'data' => [],
                ],
                [
                    'name' => '搜狗',
                    'type' => 'bar',
                    'barGap' => 0,
                    'label' => 'labelOption',
                    'emphasis' => ['focus' => 'series'],
                    'data' => [],
                ],
            ],
        ];
        $date = $request->post('date');
        // 所有指定日期段的
        $filename = "spider-$date.log";
        for ($i = 0; $i < count($host_array); $i++) {
            $host_name = $host_array[$i]['host'];
            $file_path = storage_path("logs/{$host_name}/$filename");
            $host_date_log_array['xAxis_data'][] = $host_name;
            if (file_exists($file_path)) {
                $file_get_contents = file_get_contents($file_path);
                $host_date_log_array['series'][0]['data'][] = substr_count($file_get_contents, 'Baiduspider');
                $host_date_log_array['series'][1]['data'][] = substr_count($file_get_contents, '360Spider');
                $host_date_log_array['series'][2]['data'][] = substr_count($file_get_contents, 'YisouSpider');
                $host_date_log_array['series'][3]['data'][] = substr_count($file_get_contents, 'Sogou inst spider')
                    + substr_count($file_get_contents, 'Sogou web spider')
                    + substr_count($file_get_contents, 'Sogou spider');
            } else {
                $host_date_log_array['series'][0]['data'][] = 0;
                $host_date_log_array['series'][1]['data'][] = 0;
                $host_date_log_array['series'][2]['data'][] = 0;
                $host_date_log_array['series'][3]['data'][] = 0;
            }
        }
        return json_encode($host_date_log_array);
    }


    public function get_table_data(Request $request)
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
            //检测指正是否到达文件的未端
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

    /**
     * 获取所有域名
     * @return array
     */
    public function get_all_host_fun(): array
    {
        $handler = opendir(storage_path('logs'));
        $files = [];
        while (($filename = readdir($handler)) !== false) {
            // 务必使用!==，防止目录下出现类似文件名“0”等情况
            if ($filename !== "." && $filename !== ".." && $filename !== "laravel.log" && $filename !== ".gitignore") {
                $files[] = ['host' => $filename];
            }
        }
        closedir($handler);
        return $files;
    }
}
