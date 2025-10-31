<?php

namespace App\Console\Commands;

use App\Models\Chapter;
use App\Models\Story;
use App\Models\StoryView;
use App\Models\User;
use App\Models\UserView;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SyncViewsToDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ lượt view từ Redis về database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $timestamp = now()->timestamp;

        //User View
        $processingUserViewsKey = 'UserViews:processing:' . $timestamp;
        if (!Redis::rename('UserViews', $processingUserViewsKey)) {
            // Nếu rename thất bại, có thể do key không tồn tại → kết thúc
            Log::debug('Key UserViews không tồn tại hoặc đã được xử lý trước đó.');
            return Command::SUCCESS;
        }
        $userViews = Redis::hgetall($processingUserViewsKey);
        if (!empty($userViews)) {
            foreach ($userViews as $userId => $views) {
                // Gom vào mảng để update
                User::where('id', $userId)->incrementEach(
                    [
                        'total_view' => $views,
                        'total_view_month' => $views
                    ]
                );

                UserView::where('user_id', $userId)->incrementEach(
                    [
                        'day' => $views,
                        'week' => $views,
                        'month' => $views,
                        'year' => $views,
                        'alltime' => $views,
                    ]
                );
            }
            Redis::del($processingUserViewsKey);
        }

        //Story View
        $processingStoryViewsKey = 'StoryViews:processing:' . $timestamp;
        if (!Redis::rename('StoryViews', $processingStoryViewsKey)) {
            // Nếu rename thất bại, có thể do key không tồn tại → kết thúc
            Log::debug('Key StoryViews không tồn tại hoặc đã được xử lý trước đó.');
            return Command::SUCCESS;
        }
        $storyViews = Redis::hgetall($processingStoryViewsKey);
        if (!empty($userViews)) {
            foreach ($storyViews as $storyId => $views) {
                Story::where('id', $storyId)->increment('total_view', $views);

                StoryView::where('story_id', $storyId)->incrementEach(
                    [
                        'day' => $views,
                        'week' => $views,
                        'month' => $views,
                        'year' => $views,
                        'alltime' => $views,
                    ]
                );
            }
            Redis::del($processingStoryViewsKey);
        }

        //Chapter View
        $processingChapterViewsKey = 'ChapterViews:processing:' . $timestamp;
        if (!Redis::rename('ChapterViews', $processingChapterViewsKey)) {
            // Nếu rename thất bại, có thể do key không tồn tại → kết thúc
            Log::debug('Key ChapterViews không tồn tại hoặc đã được xử lý trước đó.');
            return Command::SUCCESS;
        }
        $chapterViews = Redis::hgetall($processingChapterViewsKey);
        if (!empty($chapterViews)) {
            foreach ($chapterViews as $chapterId => $views) {
                Chapter::where('id', $chapterId)->increment('view', $views);
            }
            Redis::del($processingChapterViewsKey);
        }

        return Command::SUCCESS;
    }
}
