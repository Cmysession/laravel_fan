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
            "xAxis_data" => [], //域名
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


    public function get_table_list(Request $request)
    {
        ini_set('date.timezone', 'Asia/Shanghai');
        set_time_limit(0);
        $host = $request->post('host');
        $list_date_array = [
            'host' => $host,
            'log' => $request->get('log') ?? 'null',
            'logs' => [],
            'data' => [],
            'total' => ['bd' => 0, 'sg' => 0, 's6' => 0, 'sm' => 0],
        ];
        $logs_date_array = [];
        $handler = opendir(storage_path('logs/' . $host));;
        $i = 0; // 查询60天的
        while (($filename = readdir($handler)) !== false) {
            if ($i > 60) {
                break;
            }
            // 务必使用!==，防止目录下出现类似文件名“0”等情况
            if ($filename !== "." && $filename !== ".." && $filename !== ".gitignore") {
                // 日期
                $logs_date_array[] = $filename;
                ++$i;
            }
        }
        closedir($handler);
        rsort($logs_date_array);
        if (count($logs_date_array)) {
            $list_date_array['logs'] = $logs_date_array;
            if ($list_date_array['log'] === 'null') {
                $list_date_array['log'] = $list_date_array['logs'][0];
            }
        }

        $log_path = storage_path("logs/{$host}/{$list_date_array['log']}");
        if (file_exists($log_path)) {
            $file = fopen($log_path, "r");
            $ip_spider_array = config('ip_spider') ?? [];
            //检测指正是否到达文件的未端
            while (!feof($file)) {
                $line = fgets($file);
                if ($line) {
                    // 处理蜘蛛
                    $line_exp = explode('|', $line);
                    $line_exp_one = explode(' local.INFO: ', $line_exp[0]);
                    $useragent = $line_exp[4];
                    $ip_exp = explode('.', $line_exp[3]);
                    $ip_exp_str = $ip_exp[0] . '.' . $ip_exp[1];
                    $bot = '';
                    if (stripos($useragent, 'Baiduspider') !== false) {
                        $bot = '百度';
                        ++$list_date_array['total']['bd'];
                    } elseif (stripos($useragent, 'Sogou web spider') !== false) {
                        $bot = '搜狗 web';
                        ++$list_date_array['total']['sg'];
                    } elseif (stripos($useragent, 'Sogou inst spider') !== false) {
                        $bot = '搜狗 inst';
                        ++$list_date_array['total']['sg'];
                    } elseif (stripos($useragent, '360Spider') !== false) {
                        $bot = '360';
                        ++$list_date_array['total']['s6'];
                    } elseif (stripos($useragent, 'YisouSpider') !== false) {
                        $bot = '神马';
                        ++$list_date_array['total']['sm'];
                    } elseif (!empty($ip_spider_array[$ip_exp_str])) {
                        $bot = $ip_spider_array[$ip_exp_str];
                        if ($bot === '百度') {
                            ++$list_date_array['total']['sm'];
                        } elseif ($bot === '搜狗') {
                            ++$list_date_array['total']['sg'];
                        } elseif ($bot === '360') {
                            ++$list_date_array['total']['s6'];
                        } elseif ($bot === '神马') {
                            ++$list_date_array['total']['sm'];
                        }
                    }
                    if ($bot) {
                        $list_date_array['data'][] = [
                            'date' => date('Y-m-d H:i:s', strtotime(substr($line_exp_one[0], 1, -1))),
                            'url' => $line_exp_one[1],
                            'bot' => $bot,
                            'device' => $this->is_mobile($useragent),
                            'ip' => $line_exp[3],
                        ];
                    }
                }
            }
            fclose($file);
            $list_date_array['data'] = array_reverse($list_date_array['data']);
        }
        return json_encode($list_date_array);
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

    function is_mobile(string $agent)
    {
        static $is_mobile = null;
        if (isset($is_mobile)) {
            return $is_mobile;
        }
        if (empty($agent)) {
            $is_mobile = "-";
        } elseif (
            strpos($agent, 'Mobile') !== false
            || strpos($agent, 'Android') !== false
            || strpos($agent, 'Silk/') !== false
            || strpos($agent, 'Kindle') !== false
            || strpos($agent, 'BlackBerry') !== false
            || strpos($agent, 'Opera Mini') !== false
            || strpos($agent, 'Opera Mobi') !== false
        ) {
            $is_mobile = "M";
        } else {
            $is_mobile = "PC";
        }
        return $is_mobile;
    }
}
