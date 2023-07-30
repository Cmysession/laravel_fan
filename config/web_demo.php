<?php
return [
    'yzy345.com' => [
        // 网站相关
        'template' => 'wdj',//模板
        'prefix_status' => 0,//泛解析 1开,0关
        'prefix_path_status' => 1,//泛目录 1开,0关
        'cache_path' => 1, //缓存 1开,0关 // 蜘蛛模式才生效
        // 百度推送相关
        'baidu_status' => 0, //是否推送 1开,0关
        'baidu_number' => 10, //每日推送条数
        'baidu_site' => 'www.yzy345.com', // 推送域名 www
        'baidu_token' => 'token', //推送 token
        // 跳转相关
        'is_jump' => 0, // 1跳,0关
        '跳转状态' => 200, // 200, 302, 404
        '跳转地址' => '', // 完整地址 http | https
    ],
];
