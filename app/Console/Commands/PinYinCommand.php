<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Overtrue\Pinyin\Pinyin;
use Illuminate\Support\Facades\Storage;

class PinYinCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pyinyin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成拼英';

    protected $parameter = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $web_config = config('web') ?? [];
        $model = $web_config;
        $this->run_pinyin($model, array_keys($model));
    }

    public function run_pinyin(array $model_list, $array_keys, int $index = 0)
    {
        if (empty($array_keys[$index])) {
            die('无需执行');
        }
        $domain = $array_keys[$index];
        $model = $model_list[$domain];
        $path = "public/template/{$model['template']}/pinyin/" . str_replace(".", "_", $domain) . "_{$model['prefix_pinyin_len']}.json";
        if(Storage::disk('local')->exists($path)){
            return $this->run_pinyin($model_list, $array_keys,  ++$index);
        }
        $content_file = file_get_contents(storage_path("app/public/template/{$model['template']}/key/k.txt"));
        $title_array = explode("\n", trim(str_replace("\r", '', $content_file)));
        $pin_yin = new Pinyin();
        $str = '';
        for ($i = 0; $i < count($title_array); $i++) {
            $mb_str = $title_array[$i];
            if ($model['prefix_pinyin_len'] >= 20) {
                $pinyin = $pin_yin->abbr($mb_str);
            } else if ($model['prefix_pinyin_len'] !== 0) {
                $mb_str = mb_substr($title_array[$i], 0, $model['prefix_pinyin_len'] - 1, 'utf-8');
                $pinyin = $pin_yin->sentence($mb_str, '');
            } else {
                $pinyin = $pin_yin->sentence($mb_str, '');
            }
            $pinyin = strtolower($pinyin);
            $str .= "\"$title_array[$i]\":\"$pinyin\",";
        }
        $str = rtrim($str, ",");
        Storage::disk('local')->put($path, "{" . $str . "}");
        dump('生成 拼音: ' . $domain . '成功!');
        $this->run_pinyin($model_list, $array_keys,  ++$index);
    }
}
