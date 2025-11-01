<?php

namespace App\Console\Commands;

use App\Constants\BaseConstants;
use App\Models\CoinHistory;
use App\Models\MonthlyBenefit;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserCoin;
use Illuminate\Console\Command;

class AddBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addBonus:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add bonus for users based on their monthly views';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Check total user views
        $users = User::with(['coin'])
            ->where('total_view_month', '>=', MonthlyBenefit::VIEW_LEVEL['level_1'])
            ->where('type', User::UserType['TranslateTeam'])
            ->orderBy('total_view_month', 'DESC')
            ->get(['id', 'name', 'total_view_month', 'created_at']);

        $dataNotification = [];
        $dataCoinHistory = [];
        foreach ($users as $user) {
            $total_view_month = $user->total_view_month;
            $monthlyBenefit = MonthlyBenefit::firstOrCreate(
                [
                    'user_id' => $user->id
                ],
                [
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
                ]
            );

            $addCoin = 0;
            for ($i = 1; $i <= 7; $i++) { //dammy chỉ có 7 level
                $levelKey = 'level_' . $i;
                if ($total_view_month >= MonthlyBenefit::VIEW_LEVEL[$levelKey] && $monthlyBenefit->{$levelKey} == 0) {
                    $addCoin += MonthlyBenefit::BENEFIT[$levelKey];
                    $add_coin_reason = 'Thưởng tháng ' . date('m-Y') . ' mốc ' . number_format(MonthlyBenefit::VIEW_LEVEL[$levelKey]) . ' view: ' . number_format(MonthlyBenefit::BENEFIT[$levelKey]) . ' xu';

                    $dataCoinHistory[] = [
                        'user_id' => $user->id,
                        'coin' => MonthlyBenefit::BENEFIT[$levelKey],
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

                    $monthlyBenefit->{$levelKey} = 1;
                }
            }

            if ($addCoin > 0) {
                UserCoin::where('user_id', $user->id)->update(
                    [
                        'coin' => $user->coin->coin + $addCoin,
                        'total_coin' => $user->coin->total_coin + $addCoin,
                    ]
                );
                $monthlyBenefit->save();
                sleep(5);
            }
        }

        if (count($dataNotification)) {
            Notification::insert($dataNotification);
        }

        if (count($dataCoinHistory)) {
            CoinHistory::insert($dataCoinHistory);
        }
        return Command::SUCCESS;
    }
}
