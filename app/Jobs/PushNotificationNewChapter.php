<?php

namespace App\Jobs;

use App\Constants\BaseConstants;
use App\Models\Bookmark;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushNotificationNewChapter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1500;

    protected $story;
    protected $chapter;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($story, $chapter)
    {
        $this->story = $story;
        $this->chapter = $chapter;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $bookmarks = Bookmark::where('story_id', $this->story->id)->get();
        $array = [];
        foreach ($bookmarks as $item) {
            if ($this->story->thumbnail == '') {
                $thumbnail = asset('img/no-image.png');
            } else {
                $thumbnail = asset('images/story/thumbs/230/' . $this->story->thumbnail);
            }
            $array[] = [
                'user_id' => $item->user_id,
                'title' => $this->story->name,
                'description' => $this->chapter->name . ' vừa được đăng tải.',
                'image' => $thumbnail,
                'link' => route('chapter.detail', [$this->story->slug, $this->chapter->slug]),
                'unread' => BaseConstants::ACTIVE,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        Notification::insert($array);
    }
}
