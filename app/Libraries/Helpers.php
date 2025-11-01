<?php

namespace App\Libraries;

use App\Constants\BaseConstants;
use App\Models\Bank;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Story;
use App\Models\WithdrawRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class Helpers
{
    public static function get_settings()
    {
        return Cache::remember('business_settings', 86400, function () {
            return BusinessSetting::all();
        });
    }

    public static function get_setting($key, $default = null, $lang = false)
    {
        $settings = Cache::remember('business_settings', 86400, function () {
            return BusinessSetting::all();
        });

        if ($lang == false) {
            $setting = $settings->where('type', $key)->first();
        } else {
            $setting = $settings->where('type', $key)->where('lang', $lang)->first();
            $setting = !$setting ? $settings->where('type', $key)->first() : $setting;
        }
        return $setting == null ? $default : $setting->value;
    }

    public static function get_option_by_key($list, $key, $default = null, $lang = false)
    {
        if ($list) {
            $array_option_autos = unserialize($list['value_setting']);
            $str = "";
            if (!empty($array_option_autos)) {
                $count = count($array_option_autos);
                for ($i = 0; $i < $count; $i++) {
                    $label_text = ($array_option_autos[$i]['group_tdr']['tdr_name'] != '') ? $array_option_autos[$i]['group_tdr']['tdr_name'] : '';
                    $option_value = ($array_option_autos[$i]['group_tdr']['tdr_value'] != '') ? $array_option_autos[$i]['group_tdr']['tdr_value'] : '';
                    if ($label_text == $key) {
                        $str = stripslashes(stripslashes(base64_decode($option_value)));
                    }
                }
            }
            return $str;
        }
        return '';
    }

    public static function spanClassNameArray()
    {
        return [
            "b-758e67d623a9d83cae476516e9b9a248" => "tôi",
            "m-3818231a8643b987ac6ea312f0f77722" => "muội",
            "u-0892a8d4c06115a5f40449cc06ac4310" => "huynh",
            "x-aff9aac05dc4c194e527c99c6aba5356" => "chàng",
            "d-a79d6212dcc7a556054e9e9ee2655093" => "thiếp",
            "l-680de240ad465187844ed7c358ff656c" => "tiên sinh",
            "q-8e014d7216c14ffde0c97b88d1f6f70b" => "cậu",
            "u-dfccd732f310be1affa5b784f5115cf4" => "anh",
            "o-cad62ffadf091abc816a89bcf54b338f" => "quay",
            "b-3eb63a5ae341613d9cf4ed4ca13f7904" => "đệ",
            "j-93a75fa0bfabb27ec147380355e89b31" => "muốn",
            "b-a2e24f318c955cbb09c0da49a4382027" => "người",
            "b-4d6f234872086089a0781d85234091c8" => "rồi",
            "s-53e7ca3ac800c85fb2f52dd652b46536" => "sao",
            "u-1189f2c707b19ca5a66143a54ff28964" => "ấy",
            "y-41e011f77d8cda0f2555afab2399bb9a" => "lỗi",
            "o-516fa50e42b169126d3b3aaf8301f80c" => "hắn",
            "j-ce42180f29cf1c8c0329f7f8101cb7a1" => "nhìn",
            "y-2cf8582475dfb150654494f2255fadcb" => "có",
            "o-f38a8d70c931cd2181f39edb40684d61" => "phải",
            "w-7f6c2c7ad9f9823ed53adb3091fe4683" => "ổn",
            "u-8a4b73709b8ecfc290df345a2c464ce8" => "tốt",
            "u-f86b6c2a36b11ac82469206c7551161f" => "này",
            "f-4517da843a171d0673f32fa2ccb552ea" => "kia",
            "v-4cf03bec938d9e64c92002581c703c44" => "à",
            "c-c9ab64499be7853a2f01b43c8de63581" => "dạ",
            "e-5020d1e82cea222fe43af475382d919a" => "vâng",
            "d-259bbd38ce734db7006d8a26c3e92126" => "Đúng",
            "h-c56bd0860ed244921f854993f5657a64" => "Sai",
            "i-1fd7c953c1de569aa125c47a110584d6" => "nghe",
            "t-22d7b891db2d67b61f5453c5ac913e88" => "nói",
            "b-4eae8e2630476cb640720e7c6a905d06" => "đọc",
            "c-b1e81e7ad8e9db371f349ac4d6bcd125" => "viết",
            "v-38cc84a0083fff9b160e43f2e2457b13" => "ta",
            "n-51911a0eb08b96c08c5ec03f0feb469f" => "ra",
            "p-d0e56dcd6145b0c521de81e415da7c5d" => "vào",
            "z-2bd6efa71fd0afc2b66222937fc5817e" => "khóc",
            "n-397510de0d2de26bb6a79e002dcb6ffd" => "cười",
            "j-1feb7ff16ef94c8547de63f1ab3f0723" => "trước",
            "h-51797a6449b77936b9b37232cb623c41" => "sau",
            "k-eb540db5c0c1cf7daf14e203fb0b8d64" => "Nhưng",
            "b-1047e622bcc6a215de383d9eac22ad35" => "vậy",
            "j-7f9e2c8a50e8629596894c7a79f2b411" => "trên",
            "a-e13879c6dd4aa01692114c84b9a753c4" => "dưới",
            "m-b40587bae4b414a9ad8227e5eb79a329" => "lại",
            "f-1a13203057dfcfbbba884098049e91c2" => "đâu",
            "x-41327d5f93ec80958d58847dcbcf4cd7" => "mình",
            "b-4401c28a16b06f4565af51c37ed423dc" => "hơi",
            "v-7cbf6ce5333c33a34f2c1aa8c00a5dea" => "rất",
            "h-a0cb9a58bafdb69ae7f2db28b5543e28" => "vừa",
            "j-decbba4c255010ac9e88bdf3e73432e6" => "đẹp",
            "v-5969e571156a85276a5ba930e0258236" => "xấu",
            "e-7f99b3769115e9fbc276758421df4504" => "hay",
            "g-56e2eb08a9d648a2d919b704c49f8dbe" => "đi",
            "w-a7342bfd4c9c760774093f0f68f97eed" => "đứng",
            "o-f869a717a29542760c4a95254790a77b" => "ngồi",
            "y-9edf88a0928d0137b99ecfa420b153c8" => "nằm",
            "x-41f1d789771b6c968fcc1d63840edfc0" => "không",
            "d-b482a76846120f4cc7798e57c23f007e" => "nhau",
            "z-0ebe739a7539a0e44726b3aac2bad2b1" => "bị",
            "c-7b71e6b86d35e496939b3d3be240262f" => "khoảng",
            "r-70db15f955a0d3a4cfa430cd19570962" => "được",
            "e-a6ee35b4f3eeb6aba6f454e2e8337fd4" => "chưa",
            "m-8a9c4748cee50eb9940c73fbb37c8166" => "hoàn",
            "a-3719ff03279ed9b454afb20bfeb41324" => "toàn",
            "a-3671dd76d9a1e92c5e4bab8f8a1d8973" => "mẹ",
            "z-5b86c9d1b53fc1b6246864f9056fdf31" => "thân",
            "y-7ac1d1a525c2c21b5b8b8e6dc9eb8378" => "lần",
            "l-54d5969c4a2d32b4058f2c26aff81ad3" => "số",
            "g-bb7c1b9ee9a7f03bb69154a1971d4d64" => "thay",
            "q-40d8e30f716847735d39156962b2d222" => "đã",
            "z-cd3a600a6ca0c27322eef8d6c94682b9" => "trà",
            "k-5c0675052615b655fc7997ee8ba6eb75" => "biết",
            "w-f3d2ebf997dada6e32f1a26a9c7ac116" => "vậy",
            "p-312a829065c69ffd7fcbbef1312c6ac6" => "mọi",
        ];
    }

    public static function styleRenderFromClassName($classNames)
    {
        $styleContent = '';
        foreach($classNames as $key => $className) {
            $styleContent .= '.' . $key . ':before { content: "' . $className . '"; }';
        }
        return '<style>' . $styleContent . '</style>';
    }

    public static function replaceWithSpans(string $text, array $classNames): string {
        // Sắp xếp từ theo độ dài giảm dần (tránh từ ngắn ăn vào từ dài)
        uksort($classNames, function($a, $b) use ($classNames) {
            return mb_strlen($classNames[$b], 'UTF-8') <=> mb_strlen($classNames[$a], 'UTF-8');
        });

        foreach ($classNames as $class => $word) {
            // Regex: chỉ khớp từ đứng độc lập (không nằm trong từ khác), hỗ trợ Unicode
            $pattern = '/(?<![\p{L}\p{N}])' . preg_quote($word, '/') . '(?![\p{L}\p{N}])/u';
            $replacement = '<span class="' . $class . '"></span>';
            $text = preg_replace($pattern, $replacement, $text);
        }

        return $text;
    }

    public static function getUserIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }

        return $ip;
    }

    public static function arrayPaginator($array, $request)
    {
        $page = $request->get('page', 1);
        $perPage = 20;
        $offset = ($page * $perPage) - $perPage;
        return new LengthAwarePaginator(
            array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    public static function CryptoJSAesEncrypt($passphrase, $plain_text) {
        $salt = openssl_random_pseudo_bytes(256);
        $iv = openssl_random_pseudo_bytes(16);

        $iterations = 999;
        $key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);

        $encrypted_data = openssl_encrypt($plain_text, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);

        $data = array("_4xhbkt98c5v" => base64_encode($encrypted_data), "_0x4tvbv6vcr" => bin2hex($iv), "_0x67br4ff" => bin2hex($salt));
        return json_encode($data);
    }

    public static function get_user_by_id($sid)
    {
        $user = User::where('id', $sid)->first();
        return $user;
    }

    public static function level($coin)
    {
        $coin = intval($coin);
        if ($coin < 50000) {
            return 'Thành viên mới';
        } else if ($coin < 100000) {
            return 'VIP 1';
        } else if ($coin < 200000) {
            return 'VIP 2';
        } else if ($coin < 300000) {
            return 'VIP 3';
        } else if ($coin < 400000) {
            return 'VIP 4';
        } else if ($coin < 500000) {
            return 'VIP 5';
        } else if ($coin < 600000) {
            return 'VIP 6';
        } else if ($coin < 700000) {
            return 'VIP 7';
        } else if ($coin < 800000) {
            return 'VIP 8';
        } else if ($coin < 900000) {
            return 'VIP 9';
        } else if ($coin < 1000000) {
            return 'VIP 10';
        } else if ($coin < 1100000) {
            return 'VIP 11';
        } else if ($coin < 1200000) {
            return 'VIP 12';
        } else if ($coin < 1300000) {
            return 'VIP 13';
        } else if ($coin < 1400000) {
            return 'VIP 14';
        } else if ($coin < 1500000) {
            return 'VIP 15';
        } else if ($coin < 1600000) {
            return 'VIP 16';
        } else if ($coin < 1700000) {
            return 'VIP 17';
        } else if ($coin < 1800000) {
            return 'VIP 18';
        } else if ($coin < 1900000) {
            return 'VIP 19';
        } else if ($coin < 2000000) {
            return 'VIP 20';
        } else {
            return 'SIÊU VIP';
        }
    }

    public static function rank($coin)
    {
        $coin = intval($coin);
        if ($coin < 50000) {
            return 'Hiếm';
        } else if ($coin < 200000) {
            return 'Anh Hùng';
        } else if ($coin < 700000) {
            return 'Sử thi';
        } else if ($coin < 2000000) {
            return 'Huyền thoại';
        } else {
            return 'Thần thoại';
        }
    }

    public static function class_level($coin)
    {
        $coin = intval($coin);
        if ($coin < 50000) {
            return 'level_0';
        } else if ($coin < 100000) {
            return 'level_1';
        } else if ($coin < 200000) {
            return 'level_2';
        } else if ($coin < 300000) {
            return 'level_3';
        } else if ($coin < 400000) {
            return 'level_4';
        } else if ($coin < 500000) {
            return 'level_5';
        } else if ($coin < 600000) {
            return 'level_6';
        } else if ($coin < 700000) {
            return 'level_7';
        } else if ($coin < 800000) {
            return 'level_8';
        } else if ($coin < 900000) {
            return 'level_9';
        } else if ($coin < 1000000) {
            return 'level_10';
        } else if ($coin < 1100000) {
            return 'level_11';
        } else if ($coin < 1200000) {
            return 'level_12';
        } else if ($coin < 1300000) {
            return 'level_13';
        } else if ($coin < 1400000) {
            return 'level_14';
        } else if ($coin < 1500000) {
            return 'level_15';
        } else if ($coin < 1600000) {
            return 'level_16';
        } else if ($coin < 1700000) {
            return 'level_17';
        } else if ($coin < 1800000) {
            return 'level_18';
        } else if ($coin < 1900000) {
            return 'level_19';
        } else if ($coin < 2000000) {
            return 'level_20';
        } else {
            return 'level_top';
        }
    }

    public static function class_rank($coin)
    {
        $coin = intval($coin);
        if ($coin < 50000) {
            return 'level_1';
        } else if ($coin < 200000) {
            return 'level_8';
        } else if ($coin < 700000) {
            return 'level_9';
        } else if ($coin < 2000000) {
            return 'level_11';
        } else {
            return 'level_13';
        }
    }

    public static function generatePaymentCode()
    {
        while (true) {
            $rand3 = substr(str_shuffle("0123456789"), 0, 8);
            $code = strtoupper('PM' . $rand3);
            $check = Payment::where('code', $code)->first();
            if (!$check) {
                break;
            }
        }
        return $code;
    }

    public static function generateWithdrawCode()
    {
        while (true) {
            $rand3 = substr(str_shuffle("0123456789"), 0, 8);
            $code = strtoupper('WR' . $rand3);
            $check = WithdrawRequest::where('code', $code)->first();
            if (!$check) {
                break;
            }
        }
        return $code;
    }

    public static function generateRandomSlug($title)
    {
        while (true) {
            $randStr = substr(str_shuffle("qwertyuiopasdfghjklzxcvbnm"), 0, 4);
            $randStr = Str::slug($title) . '-' . $randStr;
            $check = Story::where('slug', $randStr)->first();
            if (!$check) {
                break;
            }
        }
        return $randStr;
    }

    public static function getListCategories()
    {
        $categories = Cache::rememberForever('list_categories', function () {
            return json_encode(Category::orderBy('name', 'ASC')
                ->get(
                    [
                        'id',
                        'name',
                        'slug'
                    ]
                ));
        });
        return json_decode($categories);
    }

    public static function getAuthorById($id)
    {
        $author = Cache::remember('author_' . $id, Carbon::now()->addMinutes(10), function () use ($id) {
            $author = User::where('id', $id)
                ->where('active', BaseConstants::ACTIVE)
                ->where('type', User::UserType['TranslateTeam'])
                ->first(
                    [
                        'id',
                        'name',
                        'avatar',
                        'total_view',
                        'total_listen',
                        'total_view_month',
                        'total_donate',
                        'created_at',
                        'updated_at',
                        'about_me',
                        'total_follow',
                        'team_signature',
                        'recommended_my_stories',
                        'view_time',
                        'bank_account',
                        'featured',
                        'super_star'
                    ]
                );
            if ($author) {
                $total_stories = Story::where('user_id', $id)
                    ->where('status', BaseConstants::ACTIVE)
                    ->count();
                $author->total_stories = ($total_stories != null) ? $total_stories : 0;
            }
            return json_encode($author);
        });
        return json_decode($author);
    }

    public static function getBanks()
    {
        $list_banks = Cache::remember('list_banks', Carbon::now()->addMinutes(60), function () {
            return json_encode(Bank::orderBy('name', 'ASC')->get());
        });
        return json_decode($list_banks);
    }

    public static function shout(string $string)
    {
        return strtoupper($string);
    }

    public static function msg_move_page($msg, $url = "back", $isExit = 1)
    {
        if ($msg) {
            echo "<script language='javascript'>alert('" . $msg . "');</script>";
        }
        if ($url) {
            switch ($url) {
                case "home" :
                    echo "<script>location.href='/'</script>";
                    break;
                case "back" :
                    echo "<script language='javascript'>history.go(-1);</script>";
                    break;
                case "close" :
                    echo "<script language='javascript'>self.close();</script>";
                    break;
                case "reload" :
                    echo "<script language='javascript'>document.location.reload();</script>";
                    break;
                case "top_opener_reload" :
                    echo "<script language='javascript'>top.opener.document.location.reload();</script>";
                    break;
                case "top_url" :
                    echo "<Script language='javascript'>top.document.location.href = '" . $url . "'</script>";
                    break;
                case "parent_reload" :
                    echo "<script language='javascript'>parent.document.location.reload();</Script>";
                    break;
                case "not":
                    echo "<script language='javascript'>alert('" . $msg . "');</script>";
                    break;
                default :
                    echo "<script language='javascript'>document.location.replace('" . $url . "');</script>";
                    break;
            }
        }
        if ($isExit) {
            exit();
        }
    }

    //Refresh url
    function move_page($url)
    {
        echo "<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
        exit;
    }

    public static function GetCurlUrlJson($url)
    {
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    // Set Here Your Requesred Headers
                    'Content-Type: application/json',
                ),
            )
        );
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        }
    }

    public static function convert_date($date, $format = 'Y.m.d')
    {
        $newDate = date($format, strtotime($date));
        $newDate = substr($newDate, 2);
        return $newDate;
    }

    public static function get_option($variable)
    {
        $List = json_decode(
            Cache::rememberForever('theme_option', function () {
                return json_encode(Setting::orderBy('updated', 'desc')->first());
            }),
            true
        );
        if ($List):
            $array_option_autos = unserialize($List['value_setting']);
            $str = "";
            if (!empty($array_option_autos)):
                $count = count($array_option_autos);
                for ($i = 0; $i < $count; $i++):
                    $label_text = ($array_option_autos[$i]['group_tdr']['tdr_name'] != '') ? $array_option_autos[$i]['group_tdr']['tdr_name'] : '';
                    $option_value = ($array_option_autos[$i]['group_tdr']['tdr_value'] != '') ? $array_option_autos[$i]['group_tdr']['tdr_value'] : '';
                    if ($label_text == $variable):
                        $str = stripslashes(stripslashes(base64_decode($option_value)));
                    endif;
                endfor;
            endif;
            return $str;
        endif;
    }

    public static function replaceCharacterChapter($content)
    {
        $content = str_replace(' giết ', ' g.i.ế.c ', $content);
        $content = str_replace(' hiếp ', ' h.i.ế.p ', $content);
        $content = str_replace(' hiếp dâm ', ' h.i.ế.p d.ă.m ', $content);
        $content = str_replace(' đâm ', ' đ.â.m ', $content);
        $content = str_replace(' đấm ', ' đ.ấ.m ', $content);
        $content = str_replace(' chém ', ' c.h.é.m ', $content);
        $content = str_replace(' vú ', ' v.ú ', $content);
        $content = str_replace(' bú ', ' b.ú ', $content);
        $content = str_replace(' đít ', ' đ.í.t ', $content);
        $content = str_replace(' địt ', ' đ.ị.t ', $content);
        $content = str_replace(' bắn ', ' b.ắ.n ', $content);
        $content = str_replace(' máu ', ' m.á.u ', $content);
        $content = str_replace(' chết ', ' c.h.ế.t ', $content);
        $content = str_replace(' ngực ', ' n.g.ự.c ', $content);
        $content = str_replace(' súng ', ' s.ú.n.g ', $content);
        $content = str_replace(' liếm ', ' l.i.ế.m ', $content);
        $content = str_replace(' mông ', ' m.ô.n.g ', $content);
        $content = str_replace(' đụ mẹ ', ' ụ m.ẹ ', $content);
        $content = str_replace(' con chó ', ' con ch.ó ', $content);
        $content = str_replace(' thằng chó ', ' thằng ch.ó ', $content);
        $content = str_replace(' dao ', ' d.a.o ', $content);
        $content = str_replace(' làm tình ', ' l.à.m t.ì.n.h ', $content);
        $content = str_replace(' tình dục ', ' t.ì.n.h d.ụ.c ', $content);
        $content = str_replace(' kích dục ', ' k.í.c.h d.ụ.c ', $content);
        $content = str_replace(' sexy ', ' s.e.x.y ', $content);
        $content = str_replace(' s.e.x ', ' s.e.x ', $content);
        $content = str_replace(' thoát y ', ' t.h.o.á.t y ', $content);
        $content = str_replace(' cắt cổ ', ' c.ắ.t c.ổ ', $content);
        $content = str_replace(' thuốc lá ', ' t.h.u.ố.c lá ', $content);
        $content = str_replace(' ma tuý ', ' m.a t.u.ý ', $content);
        $content = str_replace(' moi ruột ', ' moi r.u.ộ.t ', $content);
        $content = str_replace(' moi bụng ', ' moi b.ụ.n.g ', $content);
        $content = str_replace(' moi tim ', ' moi t.i.m ', $content);
        $content = str_replace(' móc tim ', ' m.ó.c t.i.m ', $content);
        $content = str_replace(' chặt chân ', ' c.h.ặ.t c.h.â.n ', $content);
        $content = str_replace(' chặt tay ', ' c.h.ặ.t t.a.y ', $content);
        $content = str_replace(' chặt xác ', ' c.h.ặ.t x.á.c ', $content);
        $content = str_replace(' chặt đầu ', ' c.h.ặ.t đ.ầ.u ', $content);
        $content = str_replace(' lựu đạn ', ' l.ự.u đ.ạ.n ', $content);
        $content = str_replace(' bom ', ' b.o.m ', $content);
        $content = str_replace(' cờ bạc ', ' cờ b.ạ.c ', $content);
        $content = str_replace(' khiêu dâm ', ' k.h.i.ê.u d.â.m ', $content);
        $content = str_replace(' bạo dâm ', ' b.ạ.o d.â.m ', $content);
        $content = str_replace(' cuồng dâm ', ' c.u.ồ.n.g d.â.m ', $content);
        $content = str_replace(' ấu dâm ', ' ấ.u d.â.m ', $content);
        $content = str_replace(' loạn luân ', ' l.o.ạ.n l.u.â.n ', $content);
        $content = str_replace(' bím ', ' b.í.m ', $content);
        $content = str_replace(' sinh dục ', ' s.i.n.h d.ụ.c ', $content);
        $content = str_replace(' khoả thân ', ' k.h.o.ả t.h.â.n ', $content);
        $content = str_replace(' nứng ', ' n.ứ.n.g ', $content);
        $content = str_replace(' thủ dâm ', ' t.h.ủ d.â.m ', $content);
        $content = str_replace(' tinh dịch ', ' t.i.n.h d.ị.c.h ', $content);
        $content = str_replace(' xác chết ', ' x.á.c c.h.ế.t ', $content);
        $content = str_replace(' thi thể ', ' t.h.i t.h.ể ', $content);
        $content = str_replace(' phân xác ', ' p.h.â.n x.á.c ', $content);
        $content = str_replace(' mổ xẻ ', ' m.ổ x.ẻ ', $content);
        $content = str_replace(' mổ bụng ', ' m.ổ b.ụ.n.g ', $content);
        $content = str_replace(' con đỉ ', ' con đ* ', $content);
        $content = str_replace(' chịch ', ' c.h.ị.c.h ', $content);
        $content = str_replace(' lồn ', ' l.ồ.n ', $content);
        $content = str_replace(' cặc ', ' c.ặ.c ', $content);
        $content = str_replace(' con cu ', ' c.o.n c.u ', $content);
        return $content;
    }

    public static function convertCharacterToOrigin($content)
    {
        $content = str_replace('g.i.ế.c', ' giết ', $content);
        $content = str_replace(' g.i.ế.t', ' giết', $content);
        $content = str_replace(' g/i/ế/t', ' giết', $content);
        $content = str_replace(' gi/ết', ' giết', $content);
        $content = str_replace(' g i ế t', ' giết', $content);
        $content = str_replace('G.i.ế.t ', 'Giết ', $content);
        $content = str_replace('G i ế t ', 'Giết ', $content);
        $content = str_replace('G/i/ế/t ', 'Giết ', $content);
        $content = str_replace('Gi/ết ', 'Giết ', $content);
        $content = str_replace('Giec ', 'Giết ', $content);
        $content = str_replace(' giec ', ' giết ', $content);
        $content = str_replace(' h.i.ế.p d.ă.m', ' hiếp dâm', $content);
        $content = str_replace(' h.i.ế.p', ' hiếp', $content);
        $content = str_replace(' h i ế p', ' hiếp', $content);
        $content = str_replace(' h/i/ế/p', ' hiếp', $content);
        $content = str_replace(' hi/ếp', ' hiếp', $content);
        $content = str_replace(' đ.â.m', ' đâm', $content);
        $content = str_replace(' đ â m', ' đâm', $content);
        $content = str_replace(' đ/â/m', ' đâm', $content);
        $content = str_replace(' đ.ấ.m', ' đấm', $content);
        $content = str_replace(' c.h.é.m', ' chém', $content);
        $content = str_replace(' c h é m', ' chém', $content);
        $content = str_replace(' c/h/é/m', ' chém', $content);
        $content = str_replace(' v.ú', ' vú', $content);
        $content = str_replace(' v ú', ' vú', $content);
        $content = str_replace(' v/ú', ' vú', $content);
        $content = str_replace(' b.ú', ' bú', $content);
        $content = str_replace(' đ.í.t', ' đít', $content);
        $content = str_replace(' đ.ị.t', ' địt', $content);
        $content = str_replace(' b.ắ.n', ' bắn', $content);
        $content = str_replace(' m.á.u', ' máu', $content);
        $content = str_replace(' m á u', ' máu', $content);
        $content = str_replace(' m/á/u', ' máu', $content);
        $content = str_replace(' c.h.ế.t', ' chết', $content);
        $content = str_replace(' c h ế t', ' chết', $content);
        $content = str_replace(' c/h/ế/t', ' chết', $content);
        $content = str_replace(' ch/ết', ' chết', $content);
        $content = str_replace('C.h.ế.t ', 'Chết ', $content);
        $content = str_replace('C h ế t ', 'Chết ', $content);
        $content = str_replace('C/h/ế/t ', 'Chết ', $content);
        $content = str_replace('Chec ', 'Chết ', $content);
        $content = str_replace(' chec ', ' chết ', $content);
        $content = str_replace(' đ.i.ê.n', ' điên', $content);
        $content = str_replace(' k.h.ù.n.g', ' khùng', $content);
        $content = str_replace(' n.g.ự.c', ' ngực', $content);
        $content = str_replace(' n g ự c', ' ngực', $content);
        $content = str_replace(' n/g/ự/c', ' ngực', $content);
        $content = str_replace(' s.ú.n.g', ' súng', $content);
        $content = str_replace(' s ú n g', ' súng', $content);
        $content = str_replace(' s/ú/n/g', ' súng', $content);
        $content = str_replace(' l.i.ế.m', ' liếm', $content);
        $content = str_replace(' m.ô.n.g', ' mông', $content);
        $content = str_replace(' ụ m.ẹ', ' đụ mẹ', $content);
        $content = str_replace(' con ch.ó', ' con chó', $content);
        $content = str_replace(' thằng ch.ó', ' thằng chó', $content);
        $content = str_replace(' d.a.o', ' dao', $content);
        $content = str_replace(' d/a/o', ' dao', $content);
        $content = str_replace(' d a o', ' dao', $content);
        $content = str_replace(' l.à.m t.ì.n.h', ' làm tình', $content);
        $content = str_replace(' t.ì.n.h d.ụ.c', ' tình dục', $content);
        $content = str_replace(' k.í.c.h d.ụ.c', ' kích dục', $content);
        $content = str_replace(' s.e.x.y', ' sexy', $content);
        $content = str_replace(' s.e.x', ' sex', $content);
        $content = str_replace(' s e x', ' sex', $content);
        $content = str_replace(' s/e/x', ' sex', $content);
        $content = str_replace(' t.h.o.á.t y', ' thoát y', $content);
        $content = str_replace(' c.ắ.t c.ổ', ' cắt cổ', $content);
        $content = str_replace(' c ắ t c ổ', ' cắt cổ', $content);
        $content = str_replace(' c/ắ/t c/ổ', ' cắt cổ', $content);
        $content = str_replace(' t.h.u.ố.c lá', ' thuốc lá', $content);
        $content = str_replace(' m.a t.u.ý', ' ma tuý', $content);
        $content = str_replace(' moi r.u.ộ.t', ' moi ruột', $content);
        $content = str_replace(' moi b.ụ.n.g', ' moi bụng', $content);
        $content = str_replace(' moi t.i.m', ' moi tim', $content);
        $content = str_replace(' m.ó.c t.i.m', ' móc tim', $content);
        $content = str_replace(' c.h.ặ.t c.h.â.n', ' chặt chân', $content);
        $content = str_replace(' c.h.ặ.t t.a.y', ' chặt tay', $content);
        $content = str_replace(' c.h.ặ.t x.á.c', ' chặt xác', $content);
        $content = str_replace(' c.h.ặ.t đ.ầ.u', ' chặt đầu', $content);
        $content = str_replace(' c.h.ặ.t', ' chặt', $content);
        $content = str_replace(' c/h/ặ/t', ' chặt', $content);
        $content = str_replace(' c h ặ t', ' chặt', $content);
        $content = str_replace(' l.ự.u đ.ạ.n', ' lựu đạn', $content);
        $content = str_replace(' b.o.m', ' bom', $content);
        $content = str_replace(' cờ b.ạ.c', ' cờ bạc', $content);
        $content = str_replace(' k.h.i.ê.u d.â.m', ' khiêu dâm', $content);
        $content = str_replace(' b.ạ.o d.â.m', ' bạo dâm', $content);
        $content = str_replace(' c.u.ồ.n.g d.â.m', ' cuồng dâm', $content);
        $content = str_replace(' ấ.u d.â.m', ' ấu dâm', $content);
        $content = str_replace(' l.o.ạ.n l.u.â.n', ' loạn luân', $content);
        $content = str_replace(' b.í.m', ' bím', $content);
        $content = str_replace(' s.i.n.h d.ụ.c', ' sinh dục', $content);
        $content = str_replace(' k.h.o.ả t.h.â.n', ' khoả thân', $content);
        $content = str_replace(' n.ứ.n.g', ' nứng', $content);
        $content = str_replace(' t.h.ủ d.â.m', ' thủ dâm', $content);
        $content = str_replace(' c.h.ó', ' chó', $content);
        $content = str_replace(' c/h/ó', ' chó', $content);
        $content = str_replace(' c h ó', ' chó', $content);
        $content = str_replace(' t.i.n.h d.ị.c.h', ' tinh dịch', $content);
        $content = str_replace(' x.á.c c.h.ế.t', ' xác chết', $content);
        $content = str_replace(' x/á/c c/h/ế/t', ' xác chết', $content);
        $content = str_replace(' x á c c h ế t', ' xác chết', $content);
        $content = str_replace(' xác ch/ết', ' xác chết', $content);
        $content = str_replace(' t.h.i t.h.ể', ' thi thể', $content);
        $content = str_replace(' t/h/i t/h/ể', ' thi thể', $content);
        $content = str_replace(' t h i t h ể', ' thi thể', $content);
        $content = str_replace(' p.h.â.n x.á.c', ' phân xác', $content);
        $content = str_replace(' m.ổ x.ẻ', ' mổ xẻ', $content);
        $content = str_replace(' m.ổ b.ụ.n.g', ' mổ bụng', $content);
        $content = str_replace(' con đ*', ' con đỉ', $content);
        $content = str_replace(' c.h.ị.c.h', ' chịch', $content);
        $content = str_replace(' c h ị c h', ' chịch', $content);
        $content = str_replace(' c/h/ị/c/h', ' chịch', $content);
        $content = str_replace(' l.ồ.n', ' lồn', $content);
        $content = str_replace(' c.ặ.c', ' cặc', $content);
        $content = str_replace(' c.o.n c.u', ' con cu', $content);
        $content = str_replace(' b.a.t n.a.t ', ' bắt nạt ', $content);
        $content = str_replace(' t.ự t.ử', ' tự tử', $content);
        $content = str_replace('T.ự t.ử ', 'Tự tử ', $content);
        return str_replace(' t.a.i n.ạ.n', ' tai nạn', $content);
    }

    public static function getThumbnail($path, $img_path, $width = null, $height = null, $watermark = false)
    {
        if (!File::exists(public_path("{$path}/" . $img_path))) {
            return asset('img/no-image.png');
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read(public_path($path . '/' . $img_path));
        if ($watermark) {
            $image->place(public_path('img/watermark-footer.png'), 'bottom', 0, 0);
        }
        $image->scale(width: $width);

        $dir_path = (dirname($img_path) == '.') ? "" : dirname($img_path);
        if (!File::exists(public_path("{$path}/thumbs/{$width}/{$dir_path}"))) {
            File::makeDirectory(public_path("{$path}/thumbs/{$width}/{$dir_path}"), 0775, true);
        }

        $image->save(public_path("{$path}/thumbs/{$width}/{$img_path}"));
        return asset("{$path}/thumbs/{$width}/{$img_path}");
    }

    public static function modifyImageQuality($img_path, $quality = 80)
    {
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($img_path);
            $encoded = $image->encodeByMediaType('image/jpeg', quality: $quality);
            $encoded->save($img_path);
        } catch (\Exception $e) {
            Log::debug("Cannot modify image: " . $img_path);
        }
        return true;
    }

    public static function parseComplexLinkTextarea($textarea, $key) {
        return Cache::remember($key, 86400, function () use ($textarea) {
            $lines = explode("\r\n", $textarea);

            $result = [];

            foreach ($lines as $line) {
                $line = trim($line);
                if (!$line) continue;

                [$name, $links] = array_map('trim', explode(' | ', $line, 2));
                $linkArray = array_map('trim', explode(', ', $links));
                $result[$name] = $linkArray;
            }

            $listLinks = [];
            foreach ($result as $link) {
                foreach ($link as $it) {
                    $listLinks[] = $it;
                }
            }
            return $listLinks;
        });
    }
}
?>
