<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class IndexController extends Controller
{

    public $template = null;

    /**
     * 泛域名前缀
     * @var string[]
     */
    public $prefix_array = ["shanghai" => "上海", "yunnan" => "云南", "inner_mongolia" => "内蒙古", "beijing" => "北京", "taiwan" => "台湾", "jilin" => "吉林", "sichuan" => "四川", "tianjin" => "天津", "ningxia" => "宁夏", "anhui" => "安徽", "shandong" => "山东", "shanxi" => "山西", "guangdong" => "广东", "guangxi" => "广西", "xinjiang" => "新疆", "jiangsu" => "江苏", "jiangxi" => "江西", "hebei" => "河北", "henan" => "河南", "zhejiang" => "浙江", "hainan" => "海南", "hubei" => "湖北", "hunan" => "湖南", "macao" => "澳门", "gansu" => "甘肃", "fujian" => "福建", "tibet" => "西藏", "guizhou" => "贵州", "liaoning" => "辽宁", "chongqing" => "重庆", "shaanxi" => "陕西", "qinhai" => "青海", "hong_kong" => "香港", "heilongjiang" => "黑龙江"];

    /**
     * 种类|类别
     * @var string[]
     */
    public $request_url_array = ["news", "sports", "entertainment", "technology", "business", "lifestyle", "health", "travel", "education", "food", "fashion", "beauty", "pets", "music", "art", "books", "movies", "TV shows", "gaming", "science", "nature", "automotive", "real estate", "jobs", "finance", "law", "politics", "religion", "charity", "social activism", "DIY/How-to", "homemaking", "parenting", "relationships", "wedding", "interior design", "gardening", "wildlife", "photography", "design", "architecture", "fitness", "yoga", "meditation", "psychology", "self-help", "spirituality", "astrology", "culture", "history",];

    public $nickname = ["广安静", "牵楚楚", "幸涵育", "贾嘉珍", "苦霓", "余星阑", "琦晶滢", "伊玄", "毛依波", "位欣愉", "艾令飒", "苗嫚儿", "蹇梅雪", "謇荣", "隐康宁", "詹延", "宏昊苍", "仪凌春", "寸梓倩", "独宏义", "淡湘", "海凌兰", "国豆", "袁贞", "续贞", "蓝笑寒", "连献", "佼强", "塔梓婷", "符半蕾", "旁知睿", "东郭嘉悦", "集冰冰", "谯翠岚", "子车彩", "城念梦", "麻敏叡", "蒉德明", "俎梓珊", "陈昕葳", "阚振", "揭冷松", "梅灵慧", "诺洁玉", "亥卫", "章绮烟", "钦水荷", "解源源", "皇浩丽", "扈飞扬", "析淳静", "管飞槐", "充静珊", "涂娅童", "戴凌寒", "束曜灿", "长玲琳", "叶晴岚", "阎乐逸", "樊新觉", "展谷梦", "褚浩瀚", "买水蓝", "胡婷秀", "东华池", "侍秀华", "诗婉柔", "力景胜", "高访天", "悟珊", "巧和顺", "寿泰", "习驰轩", "郦谷翠", "郝孟", "恽优悠", "抗碧蓉", "镜桐华", "士光", "友依云", "芮刚捷", "勇清逸", "瓮平宁", "尔姝艳", "厚颖初", "野阳曜", "九之卉", "仆唱", "皮冰海", "运湛蓝", "爱迎梅", "允念文", "史雪晴", "施思慧", "班飞烟", "鹿雁露", "曹山雁", "娄修", "宋白风", "营依琴", "尧曼青", "亓天佑", "柴元绿", "戎茉莉", "铁语柔", "羿日", "蚁和豫", "留亦旋", "绳碧白", "善书凝", "枚语", "胥囡", "石平乐", "郁翰飞", "前歌阑", "於高畅", "奉雨灵", "烟力言", "羽海亦", "阙秋", "度代梅", "么天青", "摩智敏", "邵冰蝶", "第五书萱", "呼延歌飞", "冼玲玲", "衡尔容", "圣笑翠", "茹嘉音", "党言", "那新霁", "张简香梅", "郯梦玉", "闻人怀寒", "才乐游", "繁依美", "巨修谨", "夏侯施诗", "堂三", "仲孙林楠", "公叔鹏天", "哀新美", "尾涛", "尹思聪", "伯暄文", "周绍元", "承迎蕾", "图门欢欣", "厍惜芹", "凭馨香", "澄如心", "毓荏", "凤竹悦", "仍良弼", "韩曼凡", "果姮", "夏尔琴", "撒丽泽", "麦晓丝", "双宵月", "律丹琴", "段代灵", "支凌蝶", "栗晓曼", "尉迟梓菱", "性香天", "帛秀媚", "侯丝柳", "操含莲", "楼文丽", "菅子薇", "糜丹红", "僧含烟", "奕智晖", "励向彤", "春鸿风", "邰书桃", "老蔚", "象敏丽", "眭艳芳", "绍懋", "殷渟", "戊玉泽", "佴静枫", "务善思", "腾艳娇", "杜碧菡", "睢璧", "花子凡", "羊笑南", "闾丘湉", "素春兰", "锺离采薇", "康涵蓄", "门碧", "斛思嘉", "敛湛蓝", "竭宛凝", "费牧歌", "赫连白梅", "贝良骥", "强承载", "亢丹彤", "丛欣荣", "军嘉福", "崇妍歌", "慎娅思", "公冶以晴", "丁渺", "甲煦", "惠心远", "枝清雅", "禄原", "己格菲", "俟安荷", "费莫琼华", "鞠盼柳", "雷灵阳", "拓跋冷雪", "黄夜云", "公兴贤", "匡醉冬", "郁优瑗", "华婉慧", "贡明俊", "功虹", "那拉婉秀", "英影", "毋令秋", "都颀", "剧烁", "席昆纶", "堵工", "舜俊雅", "泷籁", "千绿凝", "盈吉玉", "蹉昂", "红孤丹", "御怜南", "鲍曼彤", "沐思洁", "折霏", "骑立轩", "磨元枫", "泰惜玉", "萨鹏鲸", "其安筠", "宜雨琴", "僪寻雪", "逄同光", "衣安梦", "焉南蓉", "隋齐敏", "线平灵", "祭清淑", "玉思淼", "良醉易", "清牧", "翠韶华", "邬依凝", "廉松", "徭诗霜", "粘娜娜", "吾丹雪", "呼凯康", "庹含双", "奚静秀", "革芳洁", "禹星辰", "敬群", "甫翠绿", "彭定", "剑世英", "张雍", "植如风", "昂碧琴", "巫可", "乌雅柔蔓", "改忆远", "初文漪", "贵晴雪", "权理全", "隽冷之", "银觅儿", "段干夏菡", "危弘济", "粟幼霜", "偶雁菡", "袭清妙", "颛孙宛亦", "长孙凌寒", "郎古韵", "邶依萱", "浦宏远", "熊元绿", "字念蕾", "汪雅懿", "进灵安", "魏嘉月", "商清心", "望嫣然", "云含娇", "计逸云", "卢华奥", "虞天骄", "秋半香", "函化", "大韶容", "犁听南", "笃麦", "皇甫宜欣", "星端懿", "绪清绮", "犹浦和", "将代珊", "代雪晴", "朱迎彤", "沙元良", "顾依秋", "卑文轩", "纪明智", "遇运恒", "庚新知", "寻思博", "甄丁兰", "简弘义", "聊又青", "谭语雪", "潘怡月", "公西蓉城", "濮月桂", "蒲梦兰", "端景平", "镇尔", "籍宏朗", "保新梅", "貊思菱", "鲁冰双", "宰伟彦", "无之桃", "矫慧君", "姓晓灵", "綦安福", "掌蓝", "旗元芹", "仇飞英", "司徒喜", "池永嘉", "次涵畅", "释觅山", "殳兰泽", "江光亮", "潭梦竹", "潜小", "宓梓彤", "干飞白", "杞笑寒", "夙乐容", "祝睿德", "战傲晴", "少海宁", "宁密如", "农麦冬", "宗政修能", "柔婉娜", "渠英叡", "忻子实", "定天韵"];

    /**
     * 是否开启泛域名
     * @var int
     */
    public $prefix_status = 0;

    public $prefix_title = '';

    public $cache_path = 'cache';

    /**
     * yzy345.com
     * "{固定关键词}"
     * "{随机关键词}"
     * "{随机图片}"
     * "{固定图片}"
     * "{随机列表链接}"
     * "{随机详情链接}"
     * "{随机网名}"
     * "{随机数字}"
     * "{随机时间}"
     * @throws FileNotFoundException
     */

    public function __construct()
    {
        $web_config = config('web');
        if (empty($web_config[$_SERVER['SERVER_NAME']])) {
            die("<h2 style='text-align: center'> 网站未配置 </h2>");
        }
        $model = $web_config[$_SERVER['SERVER_NAME']];
        // 模板
        $this->template = $model['template'] ?? die("<h2 style='text-align: center'> 网站未配置 template </h2>");
        $this->prefix_status = $model['prefix_status'] ?? die("<h2 style='text-align: center'> 网站未配置 prefix_status </h2>");
        $this->cache_path = "public/template/$this->template/" . $this->cache_path . '/' . str_replace(".", "_", str_replace(":", "_", $_SERVER['HTTP_HOST'])) . '/' . $_SERVER['REQUEST_URI'];
        if (substr(strrchr($this->cache_path, '.'), 1) !== 'html') {
            $this->cache_path .= '.html';
        }
        $html = $this->get_file_html($this->cache_path);
        // 缓存存在直接输出
        if ($html) {
            die($html);
        }
        $prefix_title = mb_substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], '.'));
        if ($prefix_title) {
            if (!empty($this->prefix_array[$prefix_title])) {
                $this->prefix_title = $this->prefix_array[$prefix_title];
            }
        }
    }

    /**
     * 主页
     * @return void
     */
    public function index(): void
    {
        // 缓存查询
        $index_html = @file_get_contents(storage_path("app/public/template/$this->template/index.html"));
        if (!$index_html) {
            die("<h2 style='text-align: center'> index.html </h2>");
        }
        $index_html = $this->exchange($index_html);
        $this->put_file_html($this->cache_path, $index_html);
        die($index_html);

    }

    /**
     * 列表页
     * @return void
     */
    public function list(): void
    {
        $index_html = @file_get_contents(storage_path("app/public/template/$this->template/list.html"));
        if (!$index_html) {
            die("<h2 style='text-align: center'> list.html </h2>");
        }
        $index_html = $this->exchange($index_html);
        $this->put_file_html($this->cache_path, $index_html);
        die($index_html);
    }

    /**
     * 详情页
     * @return void
     */
    public function row(): void
    {

        $index_html = @file_get_contents(storage_path("app/public/template/$this->template/row.html"));
        if (!$index_html) {
            die("<h2 style='text-align: center'> row.html </h2>");
        }
        $index_html = $this->exchange($index_html);
        $this->put_file_html($this->cache_path, $index_html);
        die($index_html);
    }

    public function exchange(string $html): string
    {
        $html = $this->exchange_title_all($html);
        $html = $this->exchange_key_all($html);
        $html = $this->exchange_description_all($html);
        $html = $this->exchange_list_link($html);
        $html = $this->exchange_row_link($html);
        $html = $this->exchange_number($html);
        $html = $this->exchange_date($html);
        $html = $this->exchange_nickname($html);
        $html = $this->exchange_content($html);
        return $this->exchange_img($html);
    }

    /**
     * 随机标题
     * @param string $html
     * @return string
     */
    public function exchange_title_all(string $html): string
    {
        // 出现了几次
        $title_str_count = substr_count($html, '{随机标题}');
        $title_file = @file_get_contents(storage_path("app/public/template/$this->template/key/t.txt"));
        if (!$title_file) {
            die("<h2 style='text-align: center'> t.txt </h2>");
        }
        $title_array = explode("\n", trim($title_file));
        $title_array_count = count($title_array);
        if (!$title_array_count) {
            die("<h2 style='text-align: center'> t.txt 没数据 </h2>");
        }
        $title_fixed = $title_array[rand(0, $title_array_count - 1)];
        // 替换首页title
        $body_title = $title_fixed . '(中国' . ($this->prefix_title === '' ? '' : '·' . $this->prefix_title) . ')有限公司';
        $html = preg_replace("/{标题}/", $body_title, $html, 1);
        // 替换关键词
        $html = str_replace("{固定标题}", $title_fixed, $html);
        // 有几个替换几个
        for ($i = 0; $i < $title_str_count; $i++) {
            $html = preg_replace("/{随机标题}/", $title_array[rand(0, $title_array_count - 1)], $html, 1);
        }
        return $html;
    }

    /**
     * 替换随机标题
     * @param string $html
     * @return string
     */
    public function exchange_key_all(string $html): string
    {
        // 出现了几次
        $title_str_count = substr_count($html, '{随机关键词}');
        $title_file = @file_get_contents(storage_path("app/public/template/$this->template/key/k.txt"));
        if (!$title_file) {
            die("<h2 style='text-align: center'> t.txt </h2>");
        }
        $title_array = explode("\n", trim($title_file));
        $title_array_count = count($title_array);
        if (!$title_array_count) {
            die("<h2 style='text-align: center'> t.txt 没数据 </h2>");
        }
        $title_fixed = $title_array[rand(0, $title_array_count - 1)];
        // 替换首页title
//        $body_title = $title_fixed . '(中国' . ($this->prefix_title === '' ? '' : '·' . $this->prefix_title) . ')有限公司';
//        $html = preg_replace("/{随机关键词}/", $body_title, $html, 1);
        // 替换关键词
        $html = str_replace("{固定关键词}", $title_fixed, $html);
        // 有几个替换几个
        for ($i = 0; $i < $title_str_count; $i++) {
            $html = preg_replace("/{随机关键词}/", $title_array[rand(0, $title_array_count - 1)], $html, 1);
        }
        return $html;
    }

    /**
     * 随机描述
     * @param string $html
     * @return string
     */
    public function exchange_description_all(string $html): string
    {
        // 出现了几次
        $description_str_count = substr_count($html, '{随机描述}');
        $description_file = @file_get_contents(storage_path("app/public/template/$this->template/key/d.txt"));
        if (!$description_file) {
            die("<h2 style='text-align: center'> d.txt </h2>");
        }
        $description_array = explode("\n", trim($description_file));
        $description_array_count = count($description_array);
        if (!$description_array_count) {
            die("<h2 style='text-align: center'> d.txt 没数据 </h2>");
        }
        // 有几个替换几个
        for ($i = 0; $i < $description_str_count; $i++) {
            $html = preg_replace("/{随机描述}/", $description_array[rand(0, $description_array_count - 1)], $html, 1);
        }
        return $html;
    }


    /**
     * 随机替换图片
     * @param string $html
     * @return string
     */
    public function exchange_img(string $html): string
    {
        // 出现了几次
        $img_array = glob(storage_path("app/public/template/$this->template/img") . '/*.{jpg,pdf,png,jpeg}', GLOB_BRACE);
        $img_fixed = explode('/public', $img_array[rand(0, count($img_array) - 1)])[1];
        $html = str_replace("{固定图片}", '/storage' . $img_fixed, $html);
        $img_str_count = substr_count($html, '{随机图片}');
        // 有几个替换几个
        for ($i = 0; $i < $img_str_count; $i++) {
            $html = preg_replace("/{随机图片}/", '/storage' . explode('/public', $img_array[rand(0, $img_str_count - 1)])[1], $html, 1);
        }
        return $html;
    }

    /**
     * 随机列表链接
     * @param string $html
     * @return string
     */
    public function exchange_list_link(string $html): string
    {
        $img_str_count = substr_count($html, '{随机列表链接}');
        // 有几个替换几个
        for ($i = 0; $i < $img_str_count; $i++) {
            $prefix_str = '';
            // 泛前缀
            if ($this->prefix_status) {
                $prefix_str = array_rand($this->prefix_array);
            }
            $html = preg_replace("/{随机列表链接}/", '//' . $prefix_str . '.' . $_SERVER['SERVER_NAME'] . '/' . $this->request_url_array[rand(0, count($this->request_url_array) - 1)] . '.html', $html, 1);
        }
        return $html;
    }

    /**
     * 随机详情链接
     * @param string $html
     * @return string
     */
    public function exchange_row_link(string $html): string
    {
        $img_str_count = substr_count($html, '{随机详情链接}');
        // 有几个替换几个
        for ($i = 0; $i < $img_str_count; $i++) {
            $prefix_str = '';
            // 泛前缀
            if ($this->prefix_status) {
                $prefix_str = array_rand($this->prefix_array);
            }
            $html = preg_replace("/{随机详情链接}/", '//' . $prefix_str . '.' . $_SERVER['SERVER_NAME'] . '/' . $this->request_url_array[rand(0, count($this->request_url_array) - 1)] . '/' . rand(0, 999999) . '.html', $html, 1);
        }
        return $html;
    }

    /**
     * 随机数字
     * @param string $html
     * @return string
     */
    public function exchange_number(string $html): string
    {
        $number_count = substr_count($html, '{随机数字}');
        for ($i = 0; $i < $number_count; $i++) {
            $html = preg_replace("/{随机数字}/", rand(0, 99999), $html, 1);
        }
        return $html;
    }

    /**
     * 替换时间
     * @param string $html
     * @return string
     */
    public function exchange_date(string $html): string
    {
        $number_count = substr_count($html, '{随机时间}');
        for ($i = 0; $i < $number_count; $i++) {
            $html = preg_replace("/{随机时间}/", $this->random_date(20210420, date('Ymd')), $html, 1);
        }
        return $html;
    }

    public function random_date($begin_time, $end_time = "", $is = true)
    {
        $begin = strtotime($begin_time);
        $end = $end_time == "" ? mktime() : strtotime($end_time);
        $timestamp = rand($begin, $end);
        return $is ? date("Y-m-d H:i", $timestamp) : $timestamp;
    }

    /**
     * 随机网名
     * @param string $html
     * @return string
     */
    public function exchange_nickname(string $html): string
    {
        $number_count = substr_count($html, '{随机网名}');
        for ($i = 0; $i < $number_count; $i++) {
            $html = preg_replace("/{随机网名}/", $this->nickname[rand(0, count($this->nickname) - 1)], $html, 1);
        }
        return $html;
    }

    /**
     * 随机句子
     * @param string $html
     * @return string
     */
    public function exchange_content(string $html): string
    {
        // 出现了几次
        $content_str_count = substr_count($html, '{随机句子}');
        $content_file = @file_get_contents(storage_path("app/public/template/$this->template/key/c.txt"));
        if (!$content_file) {
            die("<h2 style='text-align: center'> c.txt </h2>");
        }
        $content_array = explode("\n", trim($content_file));
        $content_array_count = count($content_array);
        if (!$content_array_count) {
            die("<h2 style='text-align: center'> c.txt 没数据 </h2>");
        }
        // 有几个替换几个
        for ($i = 0; $i < $content_str_count; $i++) {
            $html = preg_replace("/{随机句子}/", $content_array[rand(0, $content_array_count - 1)], $html, 1);
        }
        return $html;
    }

    public function put_file_html(string $path, string $html)
    {
        Storage::disk('local')->put($path, $html);
    }

    /**
     * @throws FileNotFoundException
     */
    public function get_file_html(string $path): string
    {
        if (Storage::disk('local')->exists($path)) {
            return @Storage::disk('local')->get($path);
        }
        return false;
    }


}
