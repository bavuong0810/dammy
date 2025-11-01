<?php

namespace App\Console\Commands;

use App\Constants\BaseConstants;
use App\Models\CoinHistory;
use App\Models\MonthlyBenefit;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserCoin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ResetViewMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resetViewMonth:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset view tháng vào ngày 1 đầu tháng';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::where('type', User::UserType['TranslateTeam'])
            ->update(['featured' => 0, 'super_star' => 0, 'view_price' => 8]);
        sleep(10);

        $dataNotification = [];
        $dataCoinHistory = [];
        $users = User::with(['coin'])
            ->where('type', User::UserType['TranslateTeam'])
            ->where('total_view_month', '>', 0)
            ->where('active', BaseConstants::ACTIVE)
            ->get();

        foreach ($users as $user) {
            $addCoinView = (int)$user->total_view_month * $user->view_price;
            $addCoinListen = (int)$user->total_listen_month * $user->view_price;
            $addCoin = $addCoinView + $addCoinListen;
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

            //Cộng thưởng 1m view lần đầu tiên
            if ($user->total_view >= 1000000 && $user->one_million_views == BaseConstants::INACTIVE) {
                $addCoin = 200000;
                $add_coin_reason = 'Thưởng đạt 1.000.000 view lần đầu tiên';
                UserCoin::where('user_id', $user->id)->update(
                    [
                        'coin' => $user->coin->coin + $addCoin,
                        'total_coin' => $user->coin->total_coin + $addCoin,
                    ]
                );

                $dataCoinHistory[] = [
                    'user_id' => $user->id,
                    'coin' => $addCoin,
                    'type' => CoinHistory::Type['System'],
                    'message' => $add_coin_reason,
                    'transaction_type' => CoinHistory::TransactionType['PLUS'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $dataNotification[] = [
                    'user_id' => $user->id,
                    'title' => "Đam Mỹ",
                    'description' => $add_coin_reason,
                    'image' => asset('img/avatar-admin.png'),
                    'link' => route('user.coinHistories'),
                    'unread' => BaseConstants::ACTIVE,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                User::where('type', User::UserType['TranslateTeam'])
                    ->where('id', $user->id)
                    ->update(['one_million_views' => BaseConstants::ACTIVE]);
            }

            //trao huy hiệu nổi bật
            if ($user->total_view_month >= 500000 && $user->total_view_month < 1000000) {
                User::where('type', User::UserType['TranslateTeam'])
                    ->where('id', $user->id)
                    ->update(['featured' => BaseConstants::ACTIVE, 'view_price' => 9]);
            }
            if ($user->total_view_month >= 1000000) {
                User::where('type', User::UserType['TranslateTeam'])
                    ->where('id', $user->id)
                    ->update(['super_star' => BaseConstants::ACTIVE, 'view_price' => 10]);
            }

            Cache::pull('list_noti_' . $user->id);
        }

        Notification::insert($dataNotification);
        CoinHistory::insert($dataCoinHistory);

        //reset view for team
        User::where('type', User::UserType['TranslateTeam'])
            ->update(['total_view_month' => 0]);

        MonthlyBenefit::query()->update([
            'level_1' => 0,
            'level_2' => 0,
            'level_3' => 0,
            'level_4' => 0,
            'level_5' => 0,
            'level_6' => 0,
            'level_7' => 0,
            'level_8' => 0,
            'level_9' => 0,
            'level_10' => 0,
            'level_11' => 0,
            'level_12' => 0,
            'level_13' => 0,
            'level_14' => 0,
            'level_15' => 0,
        ]);
        return Command::SUCCESS;
    }
}
