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
        'baidu_token' => 'token', //推送 token
        // 跳转相关
        'is_jump' => 0, // 1跳,0关
        'jump_hard_status' => 401, // 200, 403, 404
        'jump_url_pc' => 'https://agg.robinmalgoire.com', // pc 完整地址 http | https
        'jump_url_m' => 'https://hh.elmtreemarketing.com', // 手机 完整地址 http | https
    ],
];
