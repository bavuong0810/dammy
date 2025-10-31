<?php

namespace App\Http\Controllers;

use App\Constants\BaseConstants;
use App\Libraries\Helpers;
use App\Models\Bookmark;
use App\Models\Chat;
use App\Models\Chapter;
use App\Models\FavouriteStory;
use App\Models\Follower;
use App\Models\Notification;
use App\Models\Story;
use App\Models\StoryView;
use App\Models\CommentStory;
use App\Models\RatingStory;
use App\Models\Report;
use App\Models\UserView;
use App\Tasks\TelegramTask;
use App\WebService\WebService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class AjaxController extends Controller
{
    private $telegramTask;
    public function __construct(){
        $this->telegramTask = new TelegramTask();
    }

    public function search(Request $request)
    {
        $search = strip_tags($request->search);
        $list = Story::orderBy('updated_at', 'DESC')
            ->where(function ($q) use ($search) {
                $q->whereHas('user', function ($query) {
                    $query->where('active', BaseConstants::ACTIVE);
                    $query->where('type', 1);
                });
                $q->where('status', BaseConstants::ACTIVE);
            })
            ->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
                $q->orWhere('slug', 'like', '%' . Str::slug($search) . '%');
                $q->orWhere('author', 'like', '%' . $search . '%');
                $q->orWhereHas('user', function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                });
            })
            ->limit(20)
            ->get();
        if (count($list)) {
            return response()->json(
                [
                    'success' => true,
                    'data' => $list
                ]
            );
        }
        return response()->json(
            [
                'success' => false,
                'message' => 'No data.'
            ]
        );
    }

    public function user_rating(Request $request)
    {
        $html = '';
        if (!Auth::guest()) {
            $story_id = $request->story_id;
            $rate = $request->rate;
            $user_id = Auth::user()->id;
            $data_rate = RatingStory::where('user_id', $user_id)
                ->where('story_id', '=', $story_id)
                ->first();
            if ($data_rate) {
                $html .= '0';
            } else {
                $new_rate = new RatingComics();
                $new_rate->story_id = $story_id;
                $new_rate->user_id = $user_id;
                $new_rate->rate = $rate;
                $new_rate->save();
                $html .= '1';

                $rate_story = RatingStory::where('story_id', $story_id)
                    ->sum('rate');
                $total_user_rate = RatingStory::where('story_id', $story_id)
                    ->count();
                if ($total_user_rate != 0) {
                    $avgRate = $rate_story / $total_user_rate;
                    $avgRate = substr($avgRate, 0, 3);
                } else {
                    $avgRate = 0;
                }
                $story = Story::where('id', $story_id)->first();
                $story->total_rate = $avgRate;
                $story->timestamps = false;
                $story->save();
            }
        } else {
            $html .= '2';
        }
        return $html;
    }

    public function storyAddView(Request $request)
    {
        $expiresAt5 = Carbon::now()->addMinutes(5);
        $expiresAt10 = Carbon::now()->addMinutes(10);
        $expiresAt15 = Carbon::now()->addMinutes(15);

        $slug = $request->story_slug;
        $userIP = Helpers::getUserIP();

        $cacheKey = 'check_user_ip_' . $userIP;
        if (Cache::has($cacheKey)) {
            $checkUserIP = json_decode(Cache::get($cacheKey), true);
            if (!is_array($checkUserIP)) {
                return response()->json(['success' => false, 'message' => 'Đừng gian lận']);
            }

            if ($checkUserIP['chapter_id'] == $request->chapter_id) {
                $check_time = now()->diffInSeconds(Carbon::parse($checkUserIP['time']));
                $story_id = $request->story_id;

                $story = json_decode(
                    Cache::remember('story_' . $slug, $expiresAt5, function () use ($slug) {
                        return json_encode(
                            Story::where('slug', $slug)
                                ->whereHas('user', function ($q) {
                                    $q->where('active', BaseConstants::ACTIVE);
                                    $q->where('type', BaseConstants::ACTIVE);
                                })
                                ->where('status', BaseConstants::ACTIVE)
                                ->first(
                                    [
                                        'id', 'user_id', 'total_chapter', 'name', 'slug', 'another_name', 'author',
                                        'categories', 'thumbnail', 'content', 'total_view', 'total_listen',
                                        'total_review', 'total_bookmark', 'total_favourite', 'rating', 'is_full', 'audio',
                                        'warning', 'creative', 'created_at', 'updated_at'
                                    ]
                                )
                        );
                    })
                );

                if ($story) {
                    $top_day_ids = Cache::remember('top_day_ids' , $expiresAt15, function () {
                        return StoryView::with(['story'])
                            ->whereHas('story.user', function ($q) {
                                $q->where('active', BaseConstants::ACTIVE);
                                $q->where('type', 1);
                            })
                            ->orderBy('day', 'DESC')
                            ->take(12)
                            ->pluck('story_id')
                            ->toArray();
                    });

                    if ($check_time >= 105) {
                        $timezone = false;
                        $currentTime = now();
                        $hours = $currentTime->hour;
                        $minutes = $currentTime->minute;

//                        if ($hours >= 1 && $hours < 7) {
//                            $percentView = 61;
//                            $timezone = true;
//                            if ($hours >= 2 && $hours < 5) {
//                                $percentView = 51;
//                                if ($hours == 2 && $minutes <= 30) $percentView = 54;
//                            }
//                        } else if($hours >= 7 && $hours < 17) {
//                            $percentView = 65;
//                            if ($hours == 7 && $minutes <= 30) {
//                                $timezone = true;
//                                $percentView = 61;
//                            }
//
//                            if (($hours == 11 || $hours == 12) && $minutes <= 30) {
//                                $timezone = true;
//                                $percentView = 56;
//                            }
//                        } else if($hours >= 17 && $hours < 19) {
//                            $percentView = 61;
//                            $timezone = true;
//                        } else if($hours >= 19 && $hours < 20) {
//                            $percentView = 65;
//                        } else if($hours >= 21 && $hours < 22) {
//                            $percentView = 63;
//                        } else {
//                            $percentView = 65;
//                        }
                        $percentView = 95;

//                        if (!$timezone) {
//                            if (in_array($story_id, $top_day_ids)) $percentView = 64;
//                        }

                        $rand = rand(1, 100);
                        if ($rand <= $percentView) {
                            //check user was read this chapter or not
                            $user_reader = Cache::get('user_' . $userIP . '_reader');
                            $userIPReader = ($user_reader != null) ? json_decode($user_reader) : [];
                            if (!in_array($request->chapter_id, $userIPReader)) {
                                $userIPReader[] = $request->chapter_id;
                                Cache::put('user_' . $userIP . '_reader', json_encode($userIPReader), $expiresAt10);
                            } else {
                                return response()->json(['success' => false, 'message' => 'Bạn đã đọc chương này.']);
                            }

                            //User view
                            Redis::hincrby('UserViews', $story->user_id, 1);

                            //Story View
                            Redis::hincrby('StoryViews', $story_id, 1);

                            //Chapter View
                            Redis::hincrby('ChapterViews', $request->chapter_id, 1);

                            /*
                            User::where('id', $story->user_id)->incrementEach(
                                [
                                    'total_view' => 1,
                                    'total_view_month' => 1
                                ]
                            );
                            UserView::where('user_id', $story->user_id)->incrementEach(
                                [
                                    'day' => 1,
                                    'week' => 1,
                                    'month' => 1,
                                    'year' => 1,
                                    'alltime' => 1,
                                ]
                            );
                            Story::where('id', $story_id)->increment('total_view', 1);
                            StoryView::where('story_id', $story_id)->incrementEach(
                                [
                                    'day' => 1,
                                    'week' => 1,
                                    'month' => 1,
                                    'year' => 1,
                                    'alltime' => 1,
                                ]
                            );
                            Chapter::where('id', $request->chapter_id)
                                ->where('story_id', $story_id)
                                ->increment('view', 1);
                            */

                            Cache::forget($cacheKey);
                            return response()->json(['success' => true]);
                        } else {
                            return response()->json(['success' => false, 'message' => 'Đừng gian lận']);
                        }
                    }
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Đừng gian lận']);
            }
        }
        return response()->json(['success' => false, 'message' => 'Đừng gian lận']);
    }

    public function favourite(Request $request)
    {
        if (!Auth::guest()) {
            $story_id = $request->story_id;
            $slug = $request->slug;
            $user = Auth::user();
            $user_id = $user->id;
            $check = FavouriteStory::where('user_id', $user_id)
                ->where('story_id', $story_id)
                ->first();
            if ($check) {
                return false;
            } else {
                FavouriteStory::create(['story_id' => $story_id, 'user_id' => $user_id]);
                Story::where('id', $story_id)->increment('total_favourite', 1);
                Cache::pull('story_' . $slug);
                Cache::pull('favourite_' . $user_id . '_' . $story_id);
                return true;
            }
        }
        return false;
    }

    public function bookmark(Request $request)
    {
        if (!Auth::guest()) {
            $story_id = $request->story_id;
            $slug = $request->slug;
            $user = Auth::user();
            $user_id = $user->id;
            $check = Bookmark::where('user_id', $user_id)
                ->where('story_id', $story_id)
                ->first();
            if ($check) {
                return false;
            } else {
                Bookmark::create(['story_id' => $story_id, 'user_id' => $user_id]);
                Story::where('id', $story_id)->increment('total_bookmark', 1);

                Cache::pull('bookmark_of_user_' . $user_id);
                Cache::pull('story_' . $slug);
                Cache::pull('bookmark_' . $user_id . '_page_1');
                Cache::pull('bookmark_' . $user_id . '_paginate_page_1');
                Cache::pull('bookmark_' . $user_id . '_page_2');
                Cache::pull('bookmark_' . $user_id . '_paginate_page_2');

                $bookmark_ids = Bookmark::where('user_id', $user_id)->pluck('story_id')->toArray();
                $expiresAt = Carbon::now()->addMinutes(10);
                Cache::put('bookmark_of_user_' . $user_id, json_encode($bookmark_ids), $expiresAt);

                return true;
            }
        } else {
            return false;
        }
        return false;
    }

    public function removeBookmark(Request $request)
    {
        if (!Auth::guest()) {
            $story_id = $request->story_id;
            $slug = $request->slug;
            $user_id = Auth::user()->id;
            Story::where('id', $story_id)->decrement('total_bookmark', 1);
            Bookmark::where('user_id', $user_id)
                ->where('story_id', $story_id)
                ->delete();
            Cache::pull('bookmark_of_user_' . $user_id);
            Cache::pull('story_' . $slug);
            Cache::pull('bookmark_' . $user_id . '_page_1');
            Cache::pull('bookmark_' . $user_id . '_paginate_page_1');
            Cache::pull('bookmark_' . $user_id . '_page_2');
            Cache::pull('bookmark_' . $user_id . '_paginate_page_2');
            Cache::pull('bookmark_' . $user_id . '_page_3');
            Cache::pull('bookmark_' . $user_id . '_paginate_page_3');

            $bookmark_ids = Bookmark::where('user_id', $user_id)->pluck('story_id')->toArray();
            $expiresAt = Carbon::now()->addMinutes(10);
            Cache::put('bookmark_of_user_' . $user_id, json_encode($bookmark_ids), $expiresAt);

            return true;
        } else {
            return false;
        }
    }

    public function followAuthor(Request $request)
    {
        if (!Auth::guest()) {
            $author_id = $request->author_id;
            $user = Auth::user();
            $user_id = $user->id;
            if ($user_id == $author_id) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Bạn không thể tự theo dõi chính mình.'
                    ]
                );
            }

            $check = Follower::where('user_id', $user_id)
                ->where('follow_user_id', $author_id)
                ->first();
            if ($check) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Bạn đã theo dõi người này.'
                    ]
                );
            } else {
                Follower::create(['follow_user_id' => $author_id, 'user_id' => $user_id]);
                User::where('id', $author_id)->increment('total_follow', 1);

                Cache::pull('author_' . $author_id);
                Cache::pull('follow_of_user_' . $user_id);
                Cache::pull('following_' . $user_id . '_page_1');
                Cache::pull('following_' . $user_id . '_paginate_page_1');
                Cache::pull('following_' . $user_id . '_page_2');
                Cache::pull('following_' . $user_id . '_paginate_page_2');
                Cache::pull('following_' . $user_id . '_page_3');
                Cache::pull('following_' . $user_id . '_paginate_page_3');

                $follower_ids = Follower::where('user_id', $user_id)->pluck('follow_user_id')->toArray();
                $expiresAt = Carbon::now()->addMinutes(10);
                Cache::put('follow_of_user_' . $user_id, json_encode($follower_ids), $expiresAt);
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Theo dõi thành công.'
                    ]
                );
            }
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để theo dõi người này.'
                ]
            );
        }
    }

    public function unfollowAuthor(Request $request)
    {
        if (!Auth::guest()) {
            $author_id = $request->author_id;
            $user_id = Auth::user()->id;
            $author = Helpers::getAuthorById($author_id);
            if ($author) {
                if ($author->total_follow > 0) {
                    User::where('id', $author_id)->decrement('total_follow', 1);
                }
            }

            Follower::where('user_id', $user_id)
                ->where('follow_user_id', $author_id)
                ->delete();

            Cache::pull('follow_of_user_' . $user_id);
            Cache::pull('author_' . $author_id);
            Cache::pull('following_' . $user_id . '_page_1');
            Cache::pull('following_' . $user_id . '_paginate_page_1');
            Cache::pull('following_' . $user_id . '_page_2');
            Cache::pull('following_' . $user_id . '_paginate_page_2');
            Cache::pull('following_' . $user_id . '_page_3');
            Cache::pull('following_' . $user_id . '_paginate_page_3');

            $follower_ids = Follower::where('user_id', $user_id)->pluck('follow_user_id')->toArray();
            $expiresAt = Carbon::now()->addMinutes(10);
            Cache::put('follow_of_user_' . $user_id, json_encode($follower_ids), $expiresAt);
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Đã bỏ theo dõi team.'
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    public function darkMode(Request $request) {
        if ($request->darkmode == 1) {
            session(['darkmode' => true]);
        } else {
            session(['darkmode' => false]);
        }
        return true;
    }

    public function processError(Request $request) {
        $story_id = strip_tags($request->story_id);
        $error = strip_tags($request->error);
        $error_chapter = strip_tags($request->error_chapter);
        $user_id = 0;
        if (Auth::check()) {
            $user_id = Auth::user()->id;
        }
        $note = strip_tags($request->error_note);

        $report = Report::create(
            [
                'user_id' => $user_id,
                'story_id' => $story_id,
                'error' => $error,
                'chapter_id' => $error_chapter,
                'status' => Report::StatusType['New'],
                'note' => $note
            ]
        );

        if ($report) {
            if (env('APP_ENV') == 'production') {
                $userIP = Helpers::getUserIP();
                $adminLink = route('admin.report.detail', $report->id);
//                $text = "<b>[" . Helpers::get_setting('company_name') . "]</b> Báo cáo lỗi\n"
//                    . "<b>IP: </b> " . $userIP . "\n"
//                    . "<b>Lỗi: </b> " . $error . "\n"
//                    . "<b>Trạng thái: </b> Mới \n"
//                    . "<b>Mô tả: </b> " . $note . " \n"
//                    . "<b>Admin link: </b>$adminLink\n";
                $text = <<<EOT
                        **[Đam Mỹ] Báo cáo lỗi**
                        **IP:** $userIP
                        **Lỗi:** $error
                        **Trạng thái:** Mới
                        **Mô tả:** $note
                        **Admin Link:** $adminLink
                        EOT;
                $this->telegramTask->sendMessage($text);
            }

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Cảm ơn bạn đã báo cáo lỗi với ' . Helpers::get_setting('seo_title') . '. Team sẽ xem xét báo cáo và sửa lỗi trong thời gian sớm nhất.'
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Lỗi khi khởi tạo báo cáo lỗi. Vui lòng thử lại'
                ]
            );
        }
    }

    public function loadMoreChat(Request $request) {
        $offset = $request->offset;
        $chats = Chat::with(
            [
                'user' => function ($q) {
                    $q->select('id', 'name', 'avatar');
                }
            ]
        )
            ->orderBy('created_at', 'DESC')
            ->limit(15)
            ->offset($offset)
            ->get()
            ->toArray();

        $list_chat = '';
        $count = count($chats);
        if ($count > 0) {
            for ($i = ($count - 1); $i >= 0; $i--) {
                $list_chat .= '<div class="li_chat" data-id="' . $chats[$i]['id'] . '" data-user-id="' . $chats[$i]['user_id'] .'">
                    <div class="avatar_chat">
                        <img src="' . asset('images/avatar/thumbs/100/' . $chats[$i]['user']['avatar']) . '"
                        onerror="this.src=\'' . asset('img/avata.png') . '\';" alt="">
                    </div>
                    <div class="info_chat">
                        <span class="time">' . WebService::time_request($chats[$i]['created_at']) . '</span>
                        <span class="content_text">
                            <span class="name level_0">' . $chats[$i]['user']['name'] . '</span><span class="text">: ' . $chats[$i]['content'] . '</span>
                        </span>
                    </div>
                </div>';
            }
        }
        return $list_chat;
    }

    public function chat(Request $request)
    {
        $message = $request->message;
        $message = strip_tags($message);
        if ($message == '') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không được để trống tin nhắn'
                ]
            );
        }
        $chat = Chat::create(
            [
                'user_id' => Auth::user()->id,
                'content' => $message
            ]
        );

        Cache::forget('chat_box');
        if ($chat) {
            $avatar = asset('img/avata.png');
            if (Auth::user()->avatar != '') {
                $avatar = asset('images/avatar/thumbs/100/' . Auth::user()->avatar);
            }
            $html = '<div class="li_chat" data-id="' . $chat->id . '" data-user-id="' . Auth::user()->id . '">
                <div class="avatar_chat">
                    <img src="' . $avatar . '"
                    onerror="this.src=\'' . asset('img/avata.png') . '\';" alt="">
                </div>
                <div class="info_chat">
                    <span class="time">' . WebService::time_request($chat->created_at) . '</span>
                    <span class="content_text">
                        <span class="name level_0">' . Auth::user()->name . '</span><span class="text">: ' . $message . '</span>
                    </span>
                </div>
            </div>';
            return response()->json(
                [
                    'success' => true,
                    'data' => $html
                ]
            );
        }

        return response()->json(
            [
                'success' => false
            ]
        );
    }

    public function postComment(Request $request)
    {
        if (!Auth::guest()) {
            $story_id = (int)$request->media_id;
            $story = Story::where('id', $story_id)->first();
            if ($story) {
                $txtContent = strip_tags($request->txtContent);
                if ($txtContent == "") {
                    echo 'Bình luận không được để trống.';
                    return false;
                }

                if ($this->checkLinksInText($txtContent) === false) {
                    echo 'Bình luận không được chứa link.';
                    return false;
                }

                $user_id = Auth::user()->id;
                if ($request->parent == '') {
                    $parent = 0;
                } else {
                    $parent = $request->parent;
                }

                $chapter_id = 0;
                if ($request->chapter_id != '') {
                    $chapter_id = $request->chapter_id;
                }
                $data = [
                    'user_id' => $user_id,
                    'content' => $txtContent,
                    'story_id' => $story_id,
                    'chapter_id' => $chapter_id,
                    'parent' => $parent,
                ];

                Cache::forget('comment_story_id_' . $story_id);
                Cache::forget('total_comments_reviews_story_id_' . $story_id);
                CommentStory::create($data);

                if ($story->thumbnail == '') {
                    $thumbnail = asset('img/no-image.png');
                } else {
                    $thumbnail = asset('images/story/thumbs/230/' . $story->thumbnail);
                }

                if ($user_id != $story->user_id) {
                    Notification::create(
                        [
                            'user_id' => $story->user_id,
                            'title' => "Bình luận mới",
                            'description' => 'Truyện ' . $story->name . ' vừa có một bình luận mới.',
                            'image' => $thumbnail,
                            'link' => route('story.detail', $story->slug),
                            'unread' => BaseConstants::ACTIVE,
                        ]
                    );
                    Cache::pull('list_noti_' . $story->user_id);
                }

                if ($parent > 0) {
                    $parentComment = CommentStory::find($parent);
                    if ($parentComment) {
                        if ($parentComment->user_id != $story->user_id) {
                            Notification::create(
                                [
                                    'user_id' => $parentComment->user_id,
                                    'title' => "Bình luận mới",
                                    'description' => Auth::user()->name . " đã trả lời bình luận của bạn",
                                    'image' => $thumbnail,
                                    'link' => route('story.detail', $story->slug),
                                    'unread' => BaseConstants::ACTIVE,
                                ]
                            );
                        }
                    }
                    Cache::pull('list_noti_' . $story->user_id);
                    Cache::pull('list_noti_' . $parentComment->user_id);
                }

                $html = 1;
            } else {
                $html = 'Không tìm thấy truyện.';
            }
        } else {
            $html = 'Bạn chưa đăng nhập.';
        }
        return $html;
    }

    private function checkLinksInText($text) {
        // Biểu thức chính quy để tìm tất cả các URL trong đoạn văn bản
        $pattern = '/https?:\/\/[^\s]+/i';

        // Tìm tất cả các liên kết trong văn bản
        preg_match_all($pattern, $text, $matches);

        // Duyệt qua từng liên kết và kiểm tra điều kiện
        foreach ($matches[0] as $url) {
            if (strpos($url, 'https://otruyen') !== 0 && strpos($url, 'http://otruyen') !== 0) {
                return false;
            }
        }

        return true;
    }

    public function showComment(Request $request)
    {
        $story_id = $request->story_id;
        return WebService::CommentRender($story_id);
    }

    public function moreComment(Request $request)
    {
        $html = '';
        $story_id = (int)$request->media_id;
        $current_page = (int)$request->current_page;
        $next_page = (int)$request->next_page;
        $offset = $current_page * 10;
        $data_comment = CommentStory::where('story_id', $story_id)
            ->where('parent', 0)
            ->orderBy('created_at', 'DESC')
            ->offset($offset)
            ->limit(10)
            ->get(
                [
                    'id',
                    'user_id',
                    'content',
                    'story_id',
                    'parent',
                    'created_at'
                ]
            );
        if ($data_comment) {
            Carbon::setLocale('vi');
            $html .= '<ul class="comments">';
            foreach ($data_comment as $row) {
                $created = date('Y-m-d H:i:s', strtotime($row->created_at));
                $row->created = Carbon::parse($created)->diffForHumans(Carbon::now());

                $users_comment = $row->user;
                if ($users_comment) {
                    if ($users_comment->avatar != "") {
                        $thumbnail = asset('images/avatar/thumbs/100/' . $users_comment->avatar);
                    } else {
                        $thumbnail = asset('img/avata.png');
                    }
                    $avt = '<img src="' . $thumbnail . '" alt="">';

                    $html .= '<li class="comment_' . $row->id . '">
                    <div class="avt_user">
                      ' . $avt . '
                    </div>
                    <div class="post-comments">
                      <p>' . $row->content . '</p>
                      <p class="meta-2">
                        <a href="javascript:void(0)"><abbr title="Thành viên">' . $users_comment->name . '</abbr></a>
                        <small class="pull-right">' . $row->created . ' · <a href="javascript:void(0)" onclick="commentReply(' . $row->story_id . ',' . $row->id . ',\'/img/avata.png\')">Trả lời</a></small></p>
                    </div>';
                    $comment_id_parent = $row->id;
                    $comment_reply = CommentStory::with(['user'])
                        ->where('story_id', $story_id)
                        ->where('parent', $comment_id_parent)
                        ->orderBy('created_at', 'ASC')
                        ->get(
                            [
                                'id',
                                'user_id',
                                'content',
                                'story_id',
                                'parent',
                                'created_at'
                            ]
                        );
                    if ($comment_reply) {
                        $html .= '<ul>';
                        foreach ($comment_reply as $item) {
                            $created = date('Y-m-d H:i:s', strtotime($item->created_at));
                            $item->created = Carbon::parse($created)->diffForHumans(Carbon::now());

                            $users_comment_child = $item->user;
                            if ($users_comment_child) {
                                if ($users_comment_child->avatar != "") {
                                    $thumbnail = asset('images/avatar/thumbs/100/' . $users_comment_child->avatar);
                                } else {
                                    $thumbnail = asset('img/avata.png');
                                }
                                $avt_rep = '<img src="' . $thumbnail . '" alt="">';

                                $html .= '<li class="comment_' . $item->id . '">
                                <div class="avt_user">
                                  ' . $avt_rep . '
                                </div>
                                <div class="post-comments">
                                  <p>' . $item->content . '</p>
                                  <p class="meta-2">
                                    <a href="javascript:void(0)"><abbr title="Thành viên">' . $users_comment_child->name . '</abbr></a>
                                    <small class="pull-right">' . $item->created . ' · <a href="javascript:void(0)" onclick="commentReply(' . $row->story_id . ',' . $comment_id_parent . ',\'/img/avata.png\')">Trả lời</a></small></p>
                                </div></li>';
                            }
                        }
                        $html .= '</ul>';
                    }
                    $html .= '</li>';
                }
            }
            $next_page_s = $next_page + 1;
            $current_page_s = $current_page + 1;
            $html .= '</ul>
                <div class="paging text-center"><button class="btn btn-sm btn-lg btn-success" onclick="more_comments(\'' . $story_id . '\',\'' . $current_page_s . '\',\'' . $next_page_s . '\');">Xem thêm 10 bình luận</button></div>
              </div>
            </div>';
            return $html;
        } else {
            return '';
        }
    }

    public function deleteChapter(Request $request)
    {
        $chapter_id = $request->chapter_id;
        $story_id = $request->story_id;
        $user_id = Auth::user()->id;

        $chapter = Chapter::where('id', $chapter_id)
            ->where('story_id', $story_id)
            ->whereHas('story.user', function ($query) use ($user_id) {
                $query->where('id', $user_id);
            })
            ->first();
        if ($chapter) {
            Chapter::where('id', $chapter_id)
                ->where('story_id', $story_id)
                ->whereHas('story.user', function ($query) use ($user_id) {
                    $query->where('id', $user_id);
                })
                ->delete();
            CommentStory::where('chapter_id', $chapter_id)->delete();

            $totalChapter = Chapter::where('story_id', $story_id)->count();
            $storyDataUpdate = [
                'total_chapter' => $totalChapter
            ];

            $lastChapter = Chapter::where('story_id', $story_id)
                ->whereHas('story.user', function ($query) use ($user_id) {
                    $query->where('id', $user_id);
                })
                ->orderBy('created_at', 'DESC')
                ->first();
            if ($lastChapter) {
                $storyDataUpdate['updated_at'] = $lastChapter->created_at;
                $storyDataUpdate['last_chapter'] = $lastChapter->name;
            }
            Story::where('id', $story_id)->update($storyDataUpdate);

            Cache::pull('pageStory_page_1');
            Cache::pull('pageStory_paginate_page_1');
            Cache::pull('homepageContent');
            Cache::pull('authorPageContent_' . $user_id . '_1');
            Cache::pull('authorPageContent_' . $user_id . '_2');
        }

        return response()->json(
            [
                'success' => true,
                'message' => 'Đã xoá ' . $request->title . '.'
            ]
        );
    }

    public function readNotification(Request $request)
    {
        $notification_id = $request->id;
        Notification::where('id', $notification_id)->update(['unread' => BaseConstants::INACTIVE]);
        Cache::pull('list_noti_' . Auth::user()->id);
        return response()->json(
            [
                'success' => true,
            ]
        );
    }

    public function makeAllReadNotification()
    {
        $user_id = Auth::user()->id;
        Notification::where('user_id', $user_id)->update(['unread' => BaseConstants::INACTIVE]);
        Cache::pull('list_noti_' . $user_id);
        return response()->json(
            [
                'success' => true,
            ]
        );
    }

    public function getChapters(Request $request) {
        $slug1 = strip_tags($request->story);
        $slug2 = strip_tags($request->chapter);

        $expiresAt5 = Carbon::now()->addMinutes(5);

        $chapter = Cache::remember($slug1 . '_chapter_' . $slug2, $expiresAt5, function () use ($slug1, $slug2) {
            $chapter = Chapter::with(
                [
                    'story' => function ($query) {
                        $query->select('id', 'name', 'slug', 'thumbnail', 'user_id', 'type', 'warning');
                    }
                ]
            )
                ->whereHas('story.user', function ($q) {
                    $q->where('active', BaseConstants::ACTIVE);
                    $q->where('type', 1);
                })
                ->whereHas('story', function ($query) use ($slug1) {
                    $query->where('slug', $slug1);
                    $query->where('status', BaseConstants::ACTIVE);
                })
                ->where('slug', $slug2)
                ->where('status', BaseConstants::ACTIVE)
                ->first();
            if ($chapter) {
                try {
                    $classNames = Helpers::spanClassNameArray();
                    $author = Helpers::getAuthorById($chapter->user_id);

                    $contentHtml = Helpers::replaceWithSpans($chapter->content, $classNames);
                    $contentHtml = htmlspecialchars_decode($contentHtml);
                    $contentHtml = str_replace('<br>', '</p><p>', $contentHtml);
                    $contentHtml = str_replace('<br/>', '</p><p>', $contentHtml);
                    if ($contentHtml != '') {
                        $chapterUrl = route('chapter.detail', [$slug1, $slug2]);
                        $addText = '<p class="signature">[Truyện được đăng tải duy nhất tại dammy.me - <a href="' . $chapterUrl . '">' . $chapterUrl . '</a>.]</p>';
                        $dom = new \DOMDocument();
                        libxml_use_internal_errors(true); // Bỏ qua lỗi HTML không hợp lệ
                        $dom->loadHTML(mb_convert_encoding($contentHtml, 'HTML-ENTITIES', 'UTF-8'));
                        libxml_clear_errors();

                        // Lấy tất cả các thẻ <p>
                        $paragraphs = $dom->getElementsByTagName('p');
                        $countP = $paragraphs->length;

                        $newParagraph = $dom->createDocumentFragment();
                        $newParagraph->appendXML($addText);

                        // Chèn đoạn text vào vị trí giữa
                        $middleIndex = floor($countP / 2);
                        $parent = $paragraphs->item($middleIndex)->parentNode;
                        $referenceNode = $paragraphs->item($middleIndex);
                        $parent->insertBefore($newParagraph, $referenceNode);

                        if (isset($author->team_signature) && $author->team_signature != '') {
                            $insertText = '<p class="signature">' . nl2br($author->team_signature) . '</p>';
                            $randomNum = rand(2, $countP);

                            $signatureParagraph = $dom->createDocumentFragment();
                            $signatureParagraph->appendXML($insertText);

                            $parent = $paragraphs->item($randomNum)->parentNode;
                            $referenceNode = $paragraphs->item($randomNum);
                            $parent->insertBefore($signatureParagraph, $referenceNode);
                        }

                        $contentHtml = $dom->saveHTML();
                        $contentHtml = str_replace('<body>', '', $contentHtml);
                        $contentHtml = str_replace('</body>', '', $contentHtml);
                        $contentHtml = str_replace('<html>', '', $contentHtml);
                        $contentHtml = str_replace('</html>', '', $contentHtml);
                    }
                } catch (\Exception $e) {
                    $contentHtml = htmlspecialchars_decode($chapter->content);
                }
                $chapter->content = $contentHtml;
            }
            return json_encode($chapter);
        });

        $chapter = json_decode($chapter);
        if ($chapter) {
            $userIP = Helpers::getUserIP();
            Cache::forget('check_user_ip_' . $userIP);
            Cache::put(
                'check_user_ip_' . $userIP,
                json_encode([
                    'ip' => $userIP,
                    'chapter_id' => $chapter->id,
                    'time' => date("Y-m-d H:i:s")
                ]),
                $expiresAt5
            );

            $list_chapters = json_decode(
                Cache::remember('list_chapters_story_id_' . $chapter->story->id, Carbon::now()->addMinutes(3), function () use ($chapter) {
                    return json_encode(
                        Chapter::where('story_id', $chapter->story->id)
                            ->where('status', BaseConstants::ACTIVE)
                            ->orderBy('created_at', 'DESC')
                            ->select(
                                'id',
                                'name',
                                'slug',
                                'view',
                                'coin',
                                'created_at'
                            )
                            ->get()
                    );
                })
            );

            //process get chapter next, prev
            $next = 0;
            $prev = 0;
            if ($list_chapters && count($list_chapters) > 0) {
                foreach ($list_chapters as $key => $item) {
                    if ($item->id == $chapter->id) {
                        $next = $key - 1;
                        $prev = $key + 1;
                        break;
                    }
                }
            }

            $next_chapter = '';
            if ($next >= 0 && isset($list_chapters[$next])) {
                $next_chapter = [
                    'storySlug' => $chapter->story->slug,
                    'chapterSlug' => $list_chapters[$next]->slug
                ];
            }
            $prev_chapter = '';
            if ($prev >= 0 && isset($list_chapters[$prev])) {
                $prev_chapter = [
                    'storySlug' => $chapter->story->slug,
                    'chapterSlug' => $list_chapters[$prev]->slug
                ];
            }

            return response()->json(
                [
                    'success' => true,
                    'html' => view('story.chapter-content', compact('chapter', 'list_chapters'))->render(),
                    'next_chapter' => $next_chapter,
                    'prev_chapter' => $prev_chapter,
                    'chapterName' => $chapter->name,
                    'stringSlug' => $chapter->story->slug . ',' . $chapter->slug,
                    'url' => route('chapter.detail', [$chapter->story->slug, $chapter->slug]),
                    'storySlug' => $chapter->story->slug,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy chương truyện'
                ]
            );
        }
    }

    public function getUserHeaderInfo(Request $request)
    {
        $user = Auth::user();
        if (isset($user->id)) {
            $user_id = $user->id;
            $notificationBlock = Cache::remember('list_noti_' . $user_id, Carbon::now()->addMinutes(5), function () use ($user_id) {
                $notifications = Notification::where('user_id', $user_id)
                    ->orderBy('created_at', 'DESC')
                    ->take(30)
                    ->get();
                $unread_notification = Notification::where('user_id', $user_id)
                    ->where('unread', BaseConstants::ACTIVE)
                    ->count();
                return view('block.user-notification', compact('notifications', 'unread_notification'))->render();
            });

            $userMenuBlock = Cache::remember('user_menu_block_' . $user_id, Carbon::now()->addMinutes(5), function () use ($user) {
                $user_coin = $user->coin->coin;
                return view('block.user-menu-block', compact('user', 'user_coin'))->render();
            });

            return response()->json([
                'success' => true,
                'notification' => $notificationBlock,
                'userMenu' => $userMenuBlock
            ]);
        }

        return response()->json([
            'success' => false,
            'notification' => '',
            'userMenu' => ''
        ]);
    }

    public function donateInfo(Request $request)
    {
        $user_id = (int)strip_tags($request->user_id);
        $author = Helpers::getAuthorById($user_id);
        if ($author) {
            if ($author->bank_account == '') {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Tác giả/Nhóm dịch này không có thông tin tài khoản ngân hàng để nhận Donate.'
                    ]
                );
            }
            $bank = json_decode($author->bank_account, true);
            if ($bank['account_name'] == '' || $bank['account_number'] == '') {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Tác giả/Nhóm dịch này không có thông tin tài khoản ngân hàng để nhận Donate.'
                    ]
                );
            }
            $accountName = $bank['account_name'];
            $templateId = env('VIETQR_TEMPLATE_ID');

            if ($bank['bank_bin'] !== '') {
                $method = 'bank';
                $qrcode = "https://img.vietqr.io/image/" . $bank['bank_bin'] . "-" . $bank['account_number'] . "-" . $templateId . ".png?amount=0&&accountName=" . $accountName . "&addInfo=Donate%20cho%20" . urlencode($author->name);
            } else {
                $method = 'momo';
                $qrcode = "https://qrcode.tec-it.com/API/QRCode?data=2|99|" . $bank['account_number'] . "|0|0|0|0|0|Donate%20cho%20" . urlencode($author->name) . "&choe=UTF-8";
            }
            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'qr_code' => $qrcode,
                        'method' => $method,
                        'account_name' => $accountName,
                        'account_number' => $bank['account_number'],
                    ]
                ]
            );
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy tác giả hoặc nhóm dịch.']);
    }
}
