<?php

namespace App\Console;

use App\Constants\BaseConstants;
use App\Models\CoinHistory;
use App\Models\Notification;
use App\Models\ReportViewDaily;
use App\Models\Story;
use App\Models\StoryPostingSchedule;
use App\Models\StoryView;
use App\Models\User;
use App\Models\UserCoin;
use App\Models\UserView;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sync:views')->everyThreeMinutes();
        $schedule->call(function () {
            $schedules = StoryPostingSchedule::orderBy('time', 'DESC')->take(25)->get();
            foreach ($schedules as $schedule) {
                if (strtotime($schedule->time) <= Carbon::now()->timestamp) {
                    Story::where('id', $schedule->story_id)
                        ->update([
                            'status' => BaseConstants::ACTIVE,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    Cache::pull('pageStory_page_1');
                    Cache::pull('pageStory_page_2');
                    Cache::pull('homepageContent');
                    $schedule->delete();
                }
            }
        })
            ->everyMinute()
            ->name('schedule:story')
            ->onOneServer();
        //reset view day
        $schedule->call(function () {
            StoryView::query()->update(
                [
                    'day' => 0
                ]
            );

            UserView::query()->update(
                [
                    'day' => 0
                ]
            );
        })
            ->dailyAt( '00:00')
            ->name('View:updateDay')
            ->onOneServer();

        //reset view week
        $schedule->call(function () {
            StoryView::query()->update(
                [
                    'week' => 0
                ]
            );

            UserView::query()->update(
                [
                    'week' => 0
                ]
            );
        })
            ->weeklyOn( 1, '00:01')
            ->name('StoryView:updateWeek')
            ->onOneServer();

        //reset view month
        $schedule->call(function () {
            StoryView::query()->update(
                [
                    'month' => 0
                ]
            );

            UserView::query()->update(
                [
                    'month' => 0
                ]
            );
        })
            ->monthlyOn( 1, '00:02')
            ->name('StoryView:updateMonth')
            ->onOneServer();

        //reset view year
        $schedule->call(function () {
            StoryView::query()->update(
                [
                    'year' => 0
                ]
            );

            UserView::query()->update(
                [
                    'year' => 0
                ]
            );
        })
            ->yearlyOn( 1, 1, '00:03')
            ->name('StoryView:updateYear')
            ->onOneServer();

        //clear read notification after 7 days
        $schedule->call(function () {
            Notification::query()->whereDate('created_at', '<', Carbon::now()->subDays(7))
                ->delete();
        })
            ->dailyAt( '00:50')
            ->name('Notification:clearReadNotification')
            ->onOneServer();

        //Report view daily
        $schedule->call(function () {
            $users = User::orderBy('total_view_month', 'DESC')
                ->where('type', User::UserType['TranslateTeam'])
                ->get();
            $total_money = 0;
            $total_view = 0;
            foreach ($users as $user) {
                $total_view += $user->total_view_month;
                $user_coin = $user->total_view_month * $user->view_price;

                $money_will_get = $user_coin;
                $total_money += $money_will_get;
            }

            $lastDay = ReportViewDaily::orderBy('id', 'DESC')
                ->first();

            $dayView = $total_view - $lastDay->total;
            $cost = $total_money - $lastDay->total_money;

            ReportViewDaily::query()->create(
                [
                    'total' => $total_view,
                    'total_money' => $total_money,
                    'day' => $dayView,
                    'cost' => $cost
                ]
            );
        })
            ->dailyAt( '00:15')
            ->name('Report:viewDaily')
            ->onOneServer();

        //reset view at day 1 of month
        $schedule->command('resetViewMonth:run')
            ->monthlyOn( 1, '00:17')
            ->name('ResetView:onDayOneOfMonth')
            ->onOneServer();

        $schedule->command('addBonus:run')
            ->everyThirtyMinutes()
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
