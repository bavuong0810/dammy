<?php

namespace App\Http\Controllers\Admin;

use App\Constants\BaseConstants;
use App\Exports\ExportTranslateTeam;
use App\Libraries\Helpers;
use App\Models\CoinHistory;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\RecommendedStory;
use App\Models\ReportViewDaily;
use App\Models\UserCoin;
use App\Models\UserView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        $query = User::orderBy('created_at', 'DESC');

        if ($request->email != '') {
            $query->where('email', 'LIKE', "%$request->email%");
        }

        if ($request->name != '') {
            $query->where('name', 'LIKE', "%$request->name%");
        }

        if ($request->type != '') {
            $query->where('type', $request->type);
        }

        $users = $query->paginate(50);
        return view('admin.user.index', compact('users'));
    }

    public function detail($id)
    {
        $detail = User::where('id', $id)->first();
        if ($detail) {
            return view('admin.user.detail', compact('detail'));
        } else {
            return redirect()->route('admin.user.index');
        }
    }

    public function store(Request $request)
    {
        $id = $request->id;

        $data = [
            'active' => (int)$request->status,
            'type' => (int)$request->type,
            'email' => $request->email,
            'username' => $request->username,
            'name' => $request->name
        ];

        if ($request->change_password) {
            $data['password'] = bcrypt($request->password);
        }

        if ((int)$request->type == User::UserType['TranslateTeam'] && !empty($request->view_price) && !empty($request->view_time)) {
            $data['view_price'] = ((int)$request->view_price >= 8) ? (int)$request->view_price : 8;
            $data['view_time'] = ((int)$request->view_time >= 105) ? (int)$request->view_time : 105;
        }

        if ((int)$request->type == User::UserType['User']) {
            $data['team_accept_time'] = null;
            $data['request_change_type'] = BaseConstants::INACTIVE;
        }

        if ($id > 0) {
            if ($request->birthday != '') {
                $data['birthday'] = $request->birthday;
            }

            User::where("id", $id)->update($data);
            /*
            if ($request->add_coin) {
                $user =  User::with(['coin'])->where("id", $id)->first();
                if ($user) {
                    UserCoin::where('user_id', $id)->update(
                        [
                            'coin' => $user->coin->coin + (int)$request->coin,
                            'total_coin' => $user->coin->total_coin + (int)$request->coin,
                        ]
                    );

                    $add_coin_reason = 'Hệ thống đã cộng ' . number_format((int)$request->coin) . ' vào tài khoản.';
                    if ($request->add_coin_reason != '') {
                        $add_coin_reason = strip_tags($request->add_coin_reason);
                    }
                    CoinHistory::create(
                        [
                            'user_id' => $id,
                            'coin' => (int)$request->coin,
                            'type' => CoinHistory::Type['System'],
                            'message' => $add_coin_reason,
                            'transaction_type' => CoinHistory::TransactionType['PLUS']
                        ]
                    );

                    Notification::create(
                        [
                            'user_id' => $id,
                            'title' => "Đam Mỹ",
                            'description' => $add_coin_reason,
                            'image' => asset('img/avatar-admin.png'),
                            'link' => route('user.coinHistories'),
                            'unread' => BaseConstants::ACTIVE,
                        ]
                    );
                }
            }
            */

            if ((int)$request->type == User::UserType['TranslateTeam']) {
                UserView::firstOrCreate(
                    [
                        'user_id' => $id
                    ],
                    [
                        'day' => 0,
                        'week' => 0,
                        'month' => 0,
                        'year' => 0,
                        'alltime' => 0,
                    ]
                );
            }

            $msg = trans('messages.update_msg', ['model' => 'Người dùng']);
            return redirect()->route('admin.user.detail', $id)->with('success_msg', $msg);
        } else {
            return redirect()->route('admin.user.index');
        }
    }

    public function translateTeams(Request $request)
    {
        $query = User::orderBy('total_view_month', 'DESC')
            ->where('type', User::UserType['TranslateTeam']);

        if ($request->email != '') {
            $query->where('email', 'LIKE', "%$request->email%");
        }

        if ($request->name != '') {
            $query->where('name', 'LIKE', "%$request->name%");
        }

        $users = $query->get();

        $total_coin = 0;
        $total_money = 0;
        $total_view = 0;
        foreach ($users as $user) {
            $total_view += $user->total_view_month;
            $user_coin = ($user->total_view_month * $user->view_price);
            $total_coin += $user_coin;

            $money_will_get = $user_coin;
            $total_money += $money_will_get;

            $user->money_will_get = $money_will_get;
            $user->user_coin = $user_coin;
        }

        $lastDayReport = ReportViewDaily::orderBy('created_at', 'DESC')->first();

        $recommended_stories = RecommendedStory::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->get();
        $total_register = 0;
        foreach ($recommended_stories as $recommended_story) {
            $data = json_decode($recommended_story->group_data, true);
            $total_register += count($data);
        }
        $totalRevenueRecommendStory = $total_register * 2000;

        $total_payment = Payment::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->where('status', 2)
            ->whereNotIn('id', [584, 587, 589, 945])
            ->sum('amount');
        return view('admin.translate-team.index', compact('users', 'total_coin', 'total_money', 'total_view', 'lastDayReport', 'totalRevenueRecommendStory', 'total_payment'));
    }

    public function exportTranslateTeams()
    {
        $users = User::orderBy('total_view_month', 'DESC')
            ->where('type', User::UserType['TranslateTeam'])
            ->get();
        $rows = [];
        foreach ($users as $user) {
            $row = [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'user_view' => $user->total_view_month,
                'convert_coin' => $user->total_view_month * $user->view_price
            ];
            $rows[] = $row;
        }
        return (new ExportTranslateTeam($rows))->download('Nhóm Dịch ' . date('d-m-Y') . '.xlsx');
    }

    public function resetView()
    {
        $dataNotification = [];
        $dataCoinHistory = [];
        $top10 = User::with(['coin'])
            ->whereNotIn('id', [1945])
            ->orderBy('total_view_month', 'DESC')
            ->where('type', User::UserType['TranslateTeam'])
            ->take(10)
            ->get();
        foreach ($top10 as $key => $topItem) {
            $addCoin = 0;
            $add_coin_reason = '';
            switch ($key) {
                case 0:
                    $addCoin = 3000000;
                    $add_coin_reason = 'Đam Mỹ Thưởng Top 1 View Tháng';
                    break;
                case 1:
                    $addCoin = 2000000;
                    $add_coin_reason = 'Đam Mỹ Thưởng Top 2 View Tháng';
                    break;
                case 2:
                    $addCoin = 1000000;
                    $add_coin_reason = 'Đam Mỹ Thưởng Top 3 View Tháng';
                    break;
                case 3:
                    $addCoin = 900000;
                    $add_coin_reason = 'Đam Mỹ Thưởng Top 4 View Tháng';
                    break;
                case 4:
                    $addCoin = 800000;
                    $add_coin_reason = 'Đam Mỹ Thưởng Top 5 View Tháng';
                    break;
                case 5:
                    $addCoin = 700000;
                    $add_coin_reason = 'Đam Mỹ Thưởng Top 6 View Tháng';
                    break;
                case 6:
                    $addCoin = 600000;
                    $add_coin_reason = 'Đam Mỹ Thưởng Top 7 View Tháng';
                    break;
                case 7:
                    $addCoin = 500000;
                    $add_coin_reason = 'Đam Mỹ Thưởng Top 8 View Tháng';
                    break;
                case 8:
                    $addCoin = 400000;
                    $add_coin_reason = 'Đam Mỹ Thưởng Top 9 View Tháng';
                    break;
                case 9:
                    $addCoin = 300000;
                    $add_coin_reason = 'Đam Mỹ Thưởng Top 10 View Tháng';
                    break;
                default:
                    break;
            }
            UserCoin::where('user_id', $topItem->id)->update(
                [
                    'coin' => $topItem->coin->coin + $addCoin,
                    'total_coin' => $topItem->coin->total_coin + $addCoin,
                ]
            );

            $dataCoinHistory[] = [
                'user_id' => $topItem->id,
                'coin' => $addCoin,
                'type' => CoinHistory::Type['System'],
                'message' => $add_coin_reason,
                'transaction_type' => CoinHistory::TransactionType['PLUS'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $dataNotification[] = [
                'user_id' => $topItem->id,
                'title' => "Đam Mỹ",
                'description' => $add_coin_reason,
                'image' => asset('img/avatar-admin.png'),
                'link' => route('user.coinHistories'),
                'unread' => BaseConstants::ACTIVE,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        $users = User::with(['coin'])
            ->where('type', User::UserType['TranslateTeam'])
            ->where('total_view_month', '>', 0)
            ->where('active', BaseConstants::ACTIVE)
            ->get();

        foreach ($users as $user) {
            $addCoinView = (int)$user->total_view_month * $user->view_price;
            $addCoin = $addCoinView;
            UserCoin::where('user_id', $user->id)->incrementEach(
                [
                    'coin' => $addCoin,
                    'total_coin' => $addCoin
                ]
            );

            if ($user->total_view_month > 0) {
                $dataCoinHistory[] = [
                    'user_id' => $user->id,
                    'coin' => $addCoinView,
                    'type' => CoinHistory::Type['System'],
                    'message' => number_format($user->total_view_month) . ' view tháng được chuyển thành ' . number_format($addCoinView) . ' xu.',
                    'transaction_type' => CoinHistory::TransactionType['PLUS'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $dataNotification[] = [
                    'user_id' => $user->id,
                    'title' => "Đam Mỹ",
                    'description' => number_format($user->total_view_month) . ' view tháng được chuyển thành ' . number_format($addCoinView) . ' xu.',
                    'image' => asset('img/avatar-admin.png'),
                    'link' => route('user.coinHistories'),
                    'unread' => BaseConstants::ACTIVE,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
            Cache::pull('list_noti_' . $user->id);
        }
        Notification::insert($dataNotification);
        CoinHistory::insert($dataCoinHistory);

        User::where('type', User::UserType['TranslateTeam'])
            ->update(['total_view_month' => 0]);
        return response()->json(
            [
                'success' => true,
                'message' => 'Lượt xem của các nhóm dịch đã được reset.'
            ]
        );
    }

    public function convertView($id)
    {
        $user = User::with(['coin'])
            ->where('type', User::UserType['TranslateTeam'])
            ->where('total_view_month', '>', 0)
            ->where('active', BaseConstants::ACTIVE)
            ->where('id', $id)
            ->first();
        if ($user) {
            $addCoin = (int)$user->total_view_month * $user->view_price;
            UserCoin::where('user_id', $user->id)->incrementEach(
                [
                    'coin' => $addCoin,
                    'total_coin' => $addCoin
                ]
            );

            CoinHistory::create(
                [
                    'user_id' => $user->id,
                    'coin' => $addCoin,
                    'type' => CoinHistory::Type['System'],
                    'message' => number_format($user->total_view_month) . ' view tháng được chuyển thành ' . number_format($addCoin) . ' xu.',
                    'transaction_type' => CoinHistory::TransactionType['PLUS']
                ]
            );

            Notification::create(
                [
                    'user_id' => $user->id,
                    'title' => "Đam Mỹ",
                    'description' => number_format($user->total_view_month) . ' view tháng được chuyển thành ' . number_format($addCoin) . ' xu.',
                    'image' => asset('img/avatar-admin.png'),
                    'link' => route('user.coinHistories'),
                    'unread' => BaseConstants::ACTIVE,
                ]
            );
            Cache::pull('list_noti_' . $user->id);

            User::where('type', User::UserType['TranslateTeam'])
                ->where('id', $id)
                ->update(['total_view_month' => 0]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Đã chuyển đổi view sang xu cho team ' . $user->name . '.'
                ]
            );
        }

        return response()->json(
            [
                'success' => false,
                'message' => 'Không tìm thấy team.'
            ]
        );
    }
}
