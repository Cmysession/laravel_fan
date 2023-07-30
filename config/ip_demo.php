<?php
return [
    'yzy345.com' => [
        'template' => 'wdj',//模板
        'prefix_status' => 0,//泛解析 1开,0关
        'prefix_path_status' => 1,//泛目录 1开,0关
        'cache_path' => 1, //缓存 1开,0关 // 蜘蛛模式才生效
        'baidu_status' => 0, //是否推送
        'baidu_number' => 10, //每日推送条数
        'baidu_site' => 'www.yzy345.com', // 推送域名 www
        'baidu_token' => 'token', //推送token
    ],
    '127.0.0.1' => [
        'template' => 'wdj',//模板
        'prefix_status' => 1,//泛解析 1开,0关
        'prefix_path_status' => 1,//泛目录 1开,0关
        'cache_path' => 0,//缓存 1开,0关  蜘蛛模式才生效
        'baidu_status' => 0, //是否推送
        'baidu_number' => 10, //每日推送条数
        'baidu_site' => '127.0.0.1', // 推送域名 www
        'baidu_token' => 'token', //推送token
    ]
];
