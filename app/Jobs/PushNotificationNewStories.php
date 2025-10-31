<?php

namespace App\Jobs;

use App\Constants\BaseConstants;
use App\Models\Follower;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;

class PushNotificationNewStories implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1500;

    protected $story;
    protected $author;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($story, $author)
    {
        $this->story = $story;
        $this->author = $author;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $followers = Follower::where('follow_user_id', $this->author->id)->get();
        $array = [];
        foreach ($followers as $item) {
            if ($this->author->avatar == '') {
                $thumbnail = asset('img/avata.png');
            } else {
                $thumbnail = asset('images/avatar/thumbs/230/' . $this->author->avatar);
            }
            $array[] = [
                'user_id' => $item->user_id,
                'title' => $this->author->name,
                'description' => $this->author->name . ' vừa ra truyện mới: ' . $this->story->name,
                'image' => $thumbnail,
                'link' => route('story.detail', [$this->story->slug]),
                'unread' => BaseConstants::ACTIVE,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            Cache::pull('list_noti_' . $item->user_id);
        }
        Notification::insert($array);
    }
}
