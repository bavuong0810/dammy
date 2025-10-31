<?php

namespace App\Http\Controllers\User;

use App\Constants\BaseConstants;
use App\Mail\SendEmailActiveAccount;
use App\Models\Bank;
use App\Models\CoinHistory;
use App\Models\Follower;
use App\Models\RequestChangeUserType;
use App\Models\Story;
use App\Models\Chapter;
use App\Models\CommentStory;
use App\Models\Donate;
use App\Models\Payment;
use App\Models\PremiumPrice;
use App\Models\User;
use App\Models\UserCoin;
use App\Models\UserPremium;
use App\Tasks\TelegramTask;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Libraries\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private $telegramTask;
    public function __construct(){
        $this->telegramTask = new TelegramTask();
    }

    public function index()
    {
        $expiresAt5 = Carbon::now()->addMinutes(5);
        $user = Auth::user();
        $user_id = $user->id;
        if (Auth::user()->type == 1) {
            $report = json_decode(
                Cache::remember('report_for_user_translate_' . $user_id, $expiresAt5, function () use ($user, $user_id) {
                    $total_comics = Story::where('user_id', $user_id)
                        ->count();
                    $total_comics_full = Story::where('user_id', $user_id)
                        ->where('is_full', 1)
                        ->count();
                    $total_views = $user->total_view_month;
                    $total_donate = Donate::where('receiver_id', $user_id)->sum('coin');
                    $top_comics = Story::where('user_id', $user_id)
                        ->orderBy('total_view', 'desc')
                        ->limit(10)
                        ->get(['id', 'name', 'slug', 'thumbnail', 'total_view', 'updated_at'])
                        ->toArray();
                    foreach ($top_comics as $key => $item) {
                        $top_comics[$key]['total_chapters'] = Chapter::where('story_id', $item['id'])->count();
                    }

                    $report = [
                        'total_comics' => $total_comics,
                        'total_comics_full' => $total_comics_full,
                        'total_views' => $total_views,
                        'total_donate' => $total_donate,
                        'top_comics' => $top_comics,
                    ];
                    return json_encode($report);
                }),
                true
            );

            $total_comics = $report['total_comics'];
            $total_comics_full = $report['total_comics_full'];
            $total_views = $report['total_views'];
            $total_donate = $report['total_donate'];
            $top_comics = $report['top_comics'];

            return view('user.home', compact('total_comics', 'total_views', 'total_donate', 'total_comics_full', 'top_comics'));
        } else {
            $report = json_decode(
                Cache::remember('report_for_user_' . $user_id, $expiresAt5, function () use ($user, $user_id) {
                    $total_deposited = Payment::where('user_id', $user_id)
                        ->where('status', Payment::Status['Confirm'])
                        ->sum('amount');
                    $total_bookmark = Bookmark::where('user_id', $user_id)->count();
                    $total_comments = CommentStory::where('user_id', $user_id)->count();;
                    $total_donate = Donate::where('user_id', $user_id)->sum('coin');

                    $report = [
                        'total_donate' => $total_donate,
                        'total_deposited' => $total_deposited,
                        'total_bookmark' => $total_bookmark,
                        'total_comments' => $total_comments,
                    ];
                    return json_encode($report);
                }),
                true
            );

            $total_donate = $report['total_donate'];
            $total_deposited = $report['total_deposited'];
            $total_bookmark = $report['total_bookmark'];
            $total_comments = $report['total_comments'];
            return view('user.home', compact('total_donate', 'total_deposited', 'total_bookmark', 'total_comments'));
        }
    }

    public function register()
    {
        if (Auth::guest()) {
            return view('user.auth.register');
        } else {
            return redirect()->route('index');
        }
    }

    public function storeUser(Request $request)
    {
        $validation_rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ];
        $messages = [
            'name.required' => 'Nhập tên hiển thị của bạn',
            'name.max' => 'Tên hiển thị tối đa 255 ký tự',
            'email.required' => 'Hãy nhập vào địa chỉ Email',
            'email.email' => 'Địa chỉ Email không đúng định dạng',
            'email.max' => 'Địa chỉ Email tối đa 255 ký tự',
            'email.unique' => 'Địa chỉ Email đã tồn tại',
            'password.required' => 'Hãy nhập mật khẩu',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không đúng'
        ];
        $validator = Validator::make($request->all(), $validation_rules, $messages);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $datetime_now = date('Y-m-d H:i:s');
        $name = strip_tags($request->name);
        if ($name == '') {
            return Redirect::back()->withErrors('Nhập tên hiển thị của bạn.');
        }

        $checkEmail = User::where('email', strip_tags($request->email))->first();
        if ($checkEmail) {
            return Redirect::back()->withErrors('Địa chỉ Email đã được sử dụng.');
        }

        $new_cus = User::create(
            [
                'name' => $name,
                'username' => strip_tags($request->email),
                'email' => strip_tags($request->email),
                'password' => bcrypt($request->password),
                'total_view' => 0,
                'total_donate' => 0,
                'total_view_month' => 0,
                'type' => User::UserType['User'],
                'active' => BaseConstants::ACTIVE,
                'last_login' => $datetime_now,
                'email_activation' => '',//base64_encode($request->email . '-' . $datetime_now)
                'email_verified_at' => date('Y-m-d H:i:s'),
                'view_price' => (int)Helpers::get_option('plus-coin-by-view'),
                'view_time' => 105
            ]
        );

        UserCoin::create(
            [
                'user_id' => $new_cus->id,
                'coin' => 0,
                'total_coin' => 0,
            ]
        );

//        $email_admin = Helpers::get_setting('admin_email');
//        $name_admin_email = Helpers::get_setting('company_name');
//        $subject_default = 'Xác nhận đăng ký tài khoản tại ' . Helpers::get_setting('company_name');
//        $data = [
//            'name' => $new_cus->name,
//            'email' => $new_cus->email,
//            'user_id' => $new_cus->id,
//            'code_active' => $new_cus->email_activation,
//            'email_admin' => $email_admin,
//            'name_email_admin' => $name_admin_email,
//            'subject_default' => $subject_default
//        ];
//
//        Mail::to($new_cus->email)->send(new SendEmailActiveAccount($data));
        Auth::login($new_cus, true);

        return redirect()->route('user.dashboard');
    }

    public function login()
    {
        return view('user.auth.login');
    }

    public function processLogin(Request $request)
    {
        $user = User::where('email', $request->email)
            ->first();
        if ($user) {
            if ($user->active == BaseConstants::ACTIVE) {
                $login = [
                    'email' => $user->email,
                    'password' => $request->password,
                ];
                if (Auth::attempt($login, true)) {
                    $user->last_login = date('Y-m-d H:i:s');
                    $user->save();
                    return redirect()->route('user.dashboard');
                } else {
                    return redirect()->route('login')->withErrors('Email hoặc Password không chính xác');
                }
            } else {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đã bị band vui lòng liên hệ quản trị viên để được hỗ trợ.');
            }
        } else {
            return redirect()->route('login')->withErrors('Email hoặc Password không chính xác');
        }
    }

    public function activeEmail()
    {
        return view('user.auth.active-account');
    }

    public function successActiveEmail()
    {
        return view('user.success-active');
    }

    public function getActiveEmailCode($user_id, $code_validate)
    {
        $user = User::where('id', $user_id)
            ->whereNull('email_verified_at')
            ->first();
        if ($user) {
            $check = $user->email_activation;
            if ($check == $code_validate) {
                User::where('id', $user_id)
                    ->update(
                        [
                            'email_activation' => '',
                            'email_verified_at' => date('Y-m-d H:i:s'),
                            'active' => BaseConstants::ACTIVE
                        ]
                    );
                return redirect()->route('user.dashboard')
                    ->with('success_msg', 'Bạn đã kích hoạt tài khoản thành công. Chúc bạn có những trải nghiệm đọc truyện tuyệt vời cùng ' . Helpers::get_setting('seo_title') . '.');
            }
        }
        return abort(404);
    }

    public function resendActiveEmail()
    {
        $user = Auth::user();
        $email_admin = Helpers::get_setting('admin_email');
        $name_admin_email = env('MAIL_FROM_NAME');
        $subject_default = 'Xác nhận đăng ký tài khoản tại ' . Helpers::get_setting('company_name');
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'user_id' => $user->id,
            'code_active' => $user->email_activation,
            'email_admin' => $email_admin,
            'name_email_admin' => $name_admin_email,
            'subject_default' => $subject_default
        ];

        Mail::to($user->email)->send(new SendEmailActiveAccount($data));
        return redirect()->back()->with(['success_msg' => 'Đã gửi email xác thực. Vui lòng kiểm tra lại email.']);
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function areBeingInterested()
    {
        $user = Auth::user();
        $user_id = $user->id;
        $expiresAt5 = Carbon::now()->addMinutes(5);

        $day = json_decode(
            Cache::remember('areBeingInterestedDay' . $user_id, $expiresAt5, function () use ($user_id) {
                $day = DB::select(
                    "SELECT s.name, s.slug, s.thumbnail, sv.day FROM story_views as sv
                join stories as s on s.id = sv.story_id
                join users as u on u.id = s.user_id
                where u.id = $user_id
                order by sv.day DESC
                limit 15"
                );
                $day = json_decode(json_encode($day), true);
                return json_encode($day);
            }),
            true
        );

        $week = json_decode(
            Cache::remember('areBeingInterestedWeek' . $user_id, $expiresAt5, function () use ($user_id) {
                $week = DB::select(
                    "SELECT s.name, s.slug, s.thumbnail, sv.week FROM story_views as sv
                join stories as s on s.id = sv.story_id
                join users as u on u.id = s.user_id
                where u.id = $user_id
                order by sv.week DESC
                limit 15"
                );
                $week = json_decode(json_encode($week), true);
                return json_encode($week);
            }),
            true
        );

        $month = json_decode(
            Cache::remember('areBeingInterestedMonth' . $user_id, $expiresAt5, function () use ($user_id) {
                $month = DB::select(
                    "SELECT s.name, s.slug, s.thumbnail, sv.month FROM story_views as sv
                join stories as s on s.id = sv.story_id
                join users as u on u.id = s.user_id
                where u.id = $user_id
                order by sv.month DESC
                limit 15"
                );
                $month = json_decode(json_encode($month), true);
                return json_encode($month);
            }),
            true
        );
        return view('user.are-being-interested', compact('day', 'week', 'month'));
    }

    public function updateProfile(Request $request)
    {
        $validation_rules = [
            'name' => 'required|max:255',
            'username' => 'required|max:255',
            'facebook' => 'max:255',
            'avatar' => 'mimes:jpg,jpeg,png'
        ];
        $messages = [
            'name.required' => 'Nhập tên hiển thị của bạn.',
            'name.max' => 'Tên hiển thị tối đa 255 ký tự.',
            'username.required' => 'Nhập username của bạn.',
            'username.max' => 'Username tối đa 255 ký tự',
            'facebook.max' => 'Link Facebook tối đa 255 ký tự',
            'avatar.mimes' => 'Chỉ upload file .jpg, png, jpeg.'
        ];
        $validator = Validator::make($request->all(), $validation_rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $name = strip_tags($request->name);
        if ($name == '') {
            return redirect()->back()->withErrors('Nhập tên hiển thị của bạn.');
        }

        $username = strip_tags($request->username);
        if ($username == '') {
            return redirect()->back()->withErrors('Nhập username của bạn.');
        }

        $about_me = strip_tags($request->about_me);
        $team_signature = strip_tags($request->team_signature);
        $facebook = strip_tags($request->facebook);
        $phone = strip_tags($request->phone);
        $id = Auth::user()->id;
        $data = [
            'name' => $name,
            'username' => $username,
            'facebook' => $facebook,
            'about_me' => $about_me,
            'phone' => $phone,
            'team_signature' => $team_signature
        ];

        //avatar
        $year = date('Y');
        $month = date('m');
        $date = date('d');
        $path_img = $year . '/' . $month . '/' . $date . '/';
        if ($request->avatar) {
            $file = $request->file('avatar');
            $name = time() . '-' . Auth::user()->id . '.jpg';
            $name = str_replace(' ', '-', $name);
            $url_folder_upload = "/images/avatar/" . $path_img;
            $file->move(public_path() . $url_folder_upload, $name);
            $avatar = $path_img . $name;
            $data['avatar'] = $avatar;

            $path = 'images/avatar';
            Helpers::getThumbnail($path, $avatar, 230, $height = null);
            Helpers::getThumbnail($path, $avatar, 100, $height = null);

            $oldAvatar = Auth::user()->avatar;
            if ($oldAvatar != '') {
                $delete_path = $_SERVER['DOCUMENT_ROOT'] . '/images/avatar/' . $oldAvatar;
                if (file_exists($delete_path)) {
                    unlink($delete_path);
                }

                $delete_path = $_SERVER['DOCUMENT_ROOT'] . '/images/avatar/thumbs/230/' . $oldAvatar;
                if (file_exists($delete_path)) {
                    unlink($delete_path);
                }
            }
        }

        if (Auth::user()->email == '' && $request->email != '') {
            $email = $request->email;
            $checkEmail = User::where('email', $email)
                ->where('id', '<>', $id)
                ->first();
            if ($checkEmail) {
                return redirect()->back()->withErrors('Email đã được sử dụng.');
            } else {
                $data['email'] = $request->email;
            }
        }

        if (Auth::user()->type == User::UserType['TranslateTeam']) {
            if ($request->payment_method == 'bank') {
                $bank = Bank::where('shortName', $request->bank_name)->first();
                $bank_account = [
                    'bank_bin' => ($bank) ? $bank->bin : '',
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_name' => $request->account_name
                ];
            } else {
                $bank_account = [
                    'bank_bin' => '',
                    'bank_name' => 'momo',
                    'account_number' => $request->phone_number,
                    'account_name' => $request->momo_account_name
                ];
            }

            $data['bank_account'] = json_encode($bank_account);
        }

        User::where("id", $id)->update($data);
        $msg = "Thông tin tài khoản đã được cập nhật";
        return redirect()->route('user.profile')->with('success_msg', $msg);
    }

    public function logoutUser()
    {
        Auth::logout();
        return redirect()->route('index');
    }

    public function changePassword()
    {
        return view('user.change-password');
    }

    public function storeChangePassword(Request $request)
    {
        $validation_rules = [
            'password' => 'required|min:6|confirmed',
        ];
        $messages = [
            'password.required' => 'Hãy nhập mật khẩu',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không đúng'
        ];
        $validator = Validator::make($request->all(), $validation_rules, $messages);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $user = Auth::user();
        $id = $user->id;
        if ($user->register_with_social == 0) {
            if (Hash::check($request->current_password, $user->password)) {
                User::where("id", $id)->update(['password' => bcrypt($request->password)]);
                $msg = "Mật khẩu đã được thay đổi";
                return redirect()->route('user.changePassword')->with('success_msg', $msg);
            } else {
                $msg = 'Mật khẩu hiện tại không chính xác';
                return redirect()->back()->withErrors($msg);
            }
        } elseif($user->register_with_social == 1 && $user->is_change_password == 1) {
            if (Hash::check($request->current_password, $user->password)) {
                User::where("id", $id)->update(['password' => bcrypt($request->password)]);
                $msg = "Mật khẩu đã được thay đổi";
                return redirect()->route('user.changePassword')->with('success_msg', $msg);
            } else {
                $msg = 'Mật khẩu hiện tại không chính xác';
                return redirect()->back()->withErrors($msg);
            }
        } else {
            User::where("id", $id)->update(
                [
                    'password' => bcrypt($request->password),
                    'is_change_password' => BaseConstants::ACTIVE
                ]
            );
            $msg = "Mật khẩu đã được thay đổi";
            return redirect()->route('user.changePassword')->with('success_msg', $msg);
        }
    }

    public function bookmark(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $page = ($request->page) ? $request->page : 1;
        $expiresAt10 = Carbon::now()->addMinutes(10);
        $data = Cache::remember('bookmark_' . $user_id . '_page_' . $page, $expiresAt10, function () use ($user_id, $expiresAt10) {
            $bookmark_ids = json_decode(
                Cache::remember('bookmark_of_user_' . $user_id, $expiresAt10, function () use ($user_id) {
                    return json_encode(Bookmark::where('user_id', $user_id)->pluck('story_id')->toArray());
                })
            );
            $list = Story::whereIn('id', $bookmark_ids)
                ->orderBy('updated_at', 'DESC')
                ->paginate(24, [
                    'name',
                    'slug',
                    'thumbnail',
                    'total_view',
                    'total_bookmark',
                    'last_chapter',
                    'audio',
                    'is_full',
                    'rating',
                    'updated_at',
                ]);
            $paginate = $list->links('vendor.pagination.rocker-pagination')->render();
            return [
                'list' => json_encode($list),
                'paginate' => $paginate
            ];
        });

        $list = json_decode($data['list']);
        $paginate = $data['paginate'];
        return view('user.bookmark', compact('list', 'paginate'));
    }

    public function following(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $page = ($request->page) ? $request->page : 1;
        $expiresAt10 = Carbon::now()->addMinutes(10);
        $data = Cache::remember('following_' . $user_id . '_paginate_page_' . $page, $expiresAt10, function () use ($user_id, $expiresAt10) {
            $follower_ids = json_decode(
                Cache::remember('follow_of_user_' . $user_id, $expiresAt10, function () use ($user_id) {
                    return json_encode(Follower::where('user_id', $user_id)->pluck('follow_user_id')->toArray());
                })
            );
            $list = User::whereIn('id', $follower_ids)
                ->orderBy('created_at', 'DESC')
                ->paginate(24, ['id', 'avatar', 'total_view', 'name', 'total_follow']);
            $paginate = $list->links('vendor.pagination.rocker-pagination')->render();
            return [
                'list' => json_encode($list),
                'paginate' => $paginate
            ];
        });
        $list = json_decode($data['list']);
        $paginate = $data['paginate'];
        return view('user.following', compact('list', 'paginate'));
    }

    public function readHistories()
    {
        return view('user.read-history');
    }

    public function buyVip()
    {
        $premiumHistories = UserPremium::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(24);

        $premiumPrices = PremiumPrice::orderBy('id', 'ASC')->get();
        return view('user.buy-vip', compact('premiumHistories', 'premiumPrices'));
    }

    public function processBuyVip(Request $request)
    {
        $addDayVIP = $request->premiumPrice;
        $premiumPrice = PremiumPrice::where('expired', $addDayVIP)->first();
        if ($premiumPrice) {
            $user_money = Auth::user()->coin->coin;
            if ($user_money >= $premiumPrice->price) {
                $user_id = Auth::user()->id;
                $date_vip = Auth::user()->premium_date;
                $today = time();
                $currentVIP = strtotime($date_vip);
                if ($today > $currentVIP) {
                    $date_vip = date('Y-m-d H:i:s');
                }

                $new_date = Carbon::parse($date_vip)->addDays($premiumPrice->expired)->toDateTimeString();
                UserCoin::where('user_id', $user_id)
                    ->update(
                        [
                            'coin' => $user_money - $premiumPrice->price,
                        ]
                    );
                CoinHistory::create(
                    [
                        'user_id' => $user_id,
                        'coin' => $premiumPrice->price,
                        'type' => CoinHistory::Type['BuyVIP'],
                        'message' => 'Bạn đã mua gói VIP ' . $premiumPrice->name . ' với giá ' . number_format($premiumPrice->price) . ' xu.',
                        'transaction_type' => CoinHistory::TransactionType['PLUS']
                    ]
                );

                User::where('id', $user_id)->update(['premium_date' => $new_date]);

                UserPremium::create(
                    [
                        'user_id' => Auth::user()->id,
                        'time' => $premiumPrice->expired,
                        'coin' => $premiumPrice->price
                    ]
                );
                $msg = 'Nạp VIP thành công! Thời hạn VIP mới của bạn là ' . $new_date;
                return redirect()->back()->with('success_msg', $msg);
            } else {
                return redirect()->back()->withErrors('Bạn không đủ Coin để mua gói VIP này! Vui lòng <a href="' . route('user.recharge') . '">nạp thêm Coin</a>.');
            }
        } else {
            return redirect()->back()->withErrors('Vui lòng chọn đúng gói VIP cần mua!');
        }
    }

    public function requestChangeUserType()
    {
        return view('user.request-change-user-type');
    }

    public function requestChangeUserTypeProcess(Request $request)
    {
        $validation_rules = [
            'phone' => 'required|max:20',
            'facebook' => 'required|max:255',
        ];
        $messages = [
            'phone.required' => 'Nhập số điện thoại của bạn',
            'phone.max' => 'Số điện thoại tối đa 20 ký tự',
            'facebook.required' => 'Nhập Facebook của bạn',
            'facebook.max' => 'Link Facebook tối đa 255 ký tự'
        ];
        $validator = Validator::make($request->all(), $validation_rules, $messages);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $note = strip_tags($request->note);
        $phone = strip_tags($request->phone);
        $facebook = strip_tags($request->facebook);

        $requestChange = RequestChangeUserType::create(
            [
                'user_id' => $user->id,
                'note' => $note,
                'phone' => $phone,
                'facebook' => $facebook,
                'status' => RequestChangeUserType::Status['New'],
            ]
        );

        if ($requestChange) {
            User::where('id', $user->id)->update(['request_change_type' => BaseConstants::ACTIVE]);
            if (env('APP_ENV') == 'production') {
                $adminLink = route('admin.requestChangeUserType.index');
                $name = $user->name;
                $email = $user->email;
//                $text = "<b>[" . Helpers::get_setting('company_name') . "]</b> Đăng ký đóng góp truyện\n"
//                    . "<b>Họ tên: </b> $name \n"
//                    . "<b>Email: </b> $email \n"
//                    . "<b>Lời nhắn: </b> $note \n"
//                    . "<b>Trạng thái: </b> Mới \n"
//                    . "<b>Admin link: </b>$adminLink\n";
                $text = <<<EOT
                        **[Đam Mỹ] Đăng ký đóng góp truyện**
                        **Họ và tên:**  $name
                        **Email:**  $email
                        **Lời nhắn:**  $note
                        **Admin link:**  $adminLink
                        EOT;
                $this->telegramTask->sendMessage($text);
            }

            $msg = 'Cảm ơn bạn đã gửi yêu cầu đóng góp và trở thành một phần của Đam Mỹ. Team sẽ xem xét và duyệt yêu cầu của bạn trong thời gian sớm nhất!';
            return redirect()->back()->with('success_msg', $msg);
        } else {
            return redirect()->back()->withInput()->withErrors('Gửi yêu cầu không thành công. Vui lòng thử lại.');
        }
    }
}
