<?php

namespace App\Jobs;

use App\Constants\BaseConstants;
use App\Libraries\Helpers;
use App\Models\CoinHistory;
use App\Models\Notification;
use App\Models\RecommendedStory;
use App\Models\Story;
use App\Models\User;
use App\Models\UserCoin;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class RegisterRecommendedStory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1500;
    private $story_id;
    private $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($story_id, $user_id)
    {
        $this->user_id = $user_id;
        $this->story_id = $story_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $today = Carbon::today();
        $tomorrow = $today->addDay();
        $user = User::with(['coin'])->where('id', $this->user_id)->first();
        if ($user) {
            $checkStory = Story::where('id', $this->story_id)
                ->where('user_id', $user->id)
                ->first();
            if ($checkStory) {
                $user_coin = $user->coin->coin;
                if ($user_coin >= 2000) {
                    $recommendedStory = RecommendedStory::where('date', $tomorrow->toDateString())
                        ->first();
                    if ($recommendedStory) {
                        $group_data = json_decode($recommendedStory->group_data, true);
                        if (count($group_data) < 12) {
                            //kiểm tra xem team đã từng đăng ký cho hôm nay hay chưa
                            $alreadyRegister = false;
                            foreach ($group_data as $register) {
                                if ($register['user_id'] == $user->id) {
                                    $alreadyRegister = true;
                                    break;
                                }
                            }
                            if (!$alreadyRegister) {
                                $group_data[] = [
                                    'user_id' => $user->id,
                                    'story_id' => $this->story_id
                                ];

                                RecommendedStory::where('date', $tomorrow->toDateString())->update(['group_data' => json_encode($group_data)]);

                                //Trừ coin của team dịch
                                UserCoin::where('user_id', $user->id)->update(
                                    [
                                        'coin' => $user->coin->coin - 2000,
                                    ]
                                );

                                CoinHistory::create(
                                    [
                                        'user_id' => $user->id,
                                        'coin' => 2000,
                                        'type' => CoinHistory::Type['RecommendedStory'],
                                        'message' => 'Bạn đã đăng ký đề cử cho truyện ' . $checkStory->name . ' cho ngày ' . $tomorrow->toDateString() . '.',
                                        'transaction_type' => CoinHistory::TransactionType['MINUS']
                                    ]
                                );

                                Notification::create(
                                    [
                                        'user_id' => $user->id,
                                        'title' => "Đăng ký đề cử ngày " . $tomorrow->toDateString(),
                                        'description' => 'Đã đăng ký đề cử truyện ' . $checkStory->name . ' cho ngày ' . $tomorrow->toDateString() . '.',
                                        'image' => asset('img/avatar-admin.png'),
                                        'link' => route('story.detail', $checkStory->slug),
                                        'unread' => BaseConstants::ACTIVE,
                                    ]
                                );
                            } else {
                                Notification::create(
                                    [
                                        'user_id' => $user->id,
                                        'title' => "Đăng ký đề cử ngày " . $tomorrow->toDateString(),
                                        'description' => 'Bạn đã có truyện đề cử cho ngày mai, không thể cử thêm truyện khác.',
                                        'image' => asset('img/avatar-admin.png'),
                                        'link' => route('user.registerRecommendedStory'),
                                        'unread' => BaseConstants::ACTIVE,
                                    ]
                                );
                            }
                        } else {
                            Notification::create(
                                [
                                    'user_id' => $user->id,
                                    'title' => "Đăng ký đề cử ngày " . $tomorrow->toDateString(),
                                    'description' => 'Danh sách đề cử đã đầy.',
                                    'image' => asset('img/avatar-admin.png'),
                                    'link' => route('user.registerRecommendedStory'),
                                    'unread' => BaseConstants::ACTIVE,
                                ]
                            );
                        }
                    } else {
                        $group_data = [];
                        $group_data[] = [
                            'user_id' => $user->id,
                            'story_id' => $this->story_id
                        ];
                        RecommendedStory::create(
                            [
                                'group_data' => json_encode($group_data),
                                'date' => $tomorrow->toDateString(),
                            ]
                        );

                        //Trừ coin của team dịch
                        UserCoin::where('user_id', $user->id)->update(
                            [
                                'coin' => $user->coin->coin - 2000,
                            ]
                        );

                        CoinHistory::create(
                            [
                                'user_id' => $user->id,
                                'coin' => 2000,
                                'type' => CoinHistory::Type['RecommendedStory'],
                                'message' => 'Bạn đã đăng ký đề cử cho truyện ' . $checkStory->name . ' cho ngày ' . $tomorrow->toDateString() . '.',
                                'transaction_type' => CoinHistory::TransactionType['MINUS']
                            ]
                        );

                        Notification::create(
                            [
                                'user_id' => $user->id,
                                'title' => "Đăng ký đề cử ngày " . $tomorrow->toDateString(),
                                'description' => 'Đã đăng ký đề cử truyện ' . $checkStory->name . ' cho ngày ' . $tomorrow->toDateString() . '.',
                                'image' => asset('img/avatar-admin.png'),
                                'link' => route('story.detail', $checkStory->slug),
                                'unread' => BaseConstants::ACTIVE,
                            ]
                        );
                    }
                    Cache::pull('list_noti_' . $user->id);
                } else {
                    Notification::create(
                        [
                            'user_id' => $user->id,
                            'title' => "Đăng ký đề cử ngày " . $tomorrow->toDateString(),
                            'description' => 'Đăng ký đề cử không thành công vì tài khoản của bạn không đủ xu.',
                            'image' => asset('img/avatar-admin.png'),
                            'link' => route('user.registerRecommendedStory'),
                            'unread' => BaseConstants::ACTIVE,
                        ]
                    );
                }
            }
        }
    }
}
