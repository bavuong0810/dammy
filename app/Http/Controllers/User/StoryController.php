<?php

namespace App\Http\Controllers\User;

use App\Constants\BaseConstants;
use App\Jobs\PushNotificationNewStories;
use App\Jobs\RegisterRecommendedStory;
use App\Libraries\Helpers;
use App\Models\Bookmark;
use App\Models\Chapter;
use App\Models\CoinHistory;
use App\Models\CommentStory;
use App\Models\Donate;
use App\Models\Notification;
use App\Models\RecommendedStory;
use App\Models\Report;
use App\Models\StoryPostingSchedule;
use App\Models\User;
use App\Models\UserChapter;
use App\Models\UserCoin;
use App\Tasks\TelegramTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

class StoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $telegramTask;
    public function __construct(){
        $this->telegramTask = new TelegramTask();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $cacheKey = 'user_story_list_' . $user->id .'_' . md5(json_encode($request->all()));
        $list = Cache::tags(['user_story_list_' . $user->id])->remember($cacheKey, Carbon::now()->addMinutes(5), function () use ($request, $user) {
            $query = Story::select(
                'id',
                'name',
                'slug',
                'thumbnail',
                'created_at',
                'total_view',
                'updated_at',
                'last_chapter',
                'status',
                'is_full',
                'warning',
                'creative',
            )
                ->orderBy('updated_at', 'DESC')
                ->where('user_id', $user->id);

            if (isset($request->search) && $request->search != '') {
                $query->where('name', 'LIKE', '%' . $request->search . '%');
            }

            if (isset($request->category) && $request->category != '') {
                $query->whereJsonContains('categories', (string)$request->category);
            }

            return $query->paginate(20);
        });

        return view('user.story.index', compact('list'));
    }

    public function create()
    {
        return view('user.story.single');
    }

    public function detail(Request $request, $id)
    {
        $story = Story::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->first();
        if ($story) {
            $schedule = StoryPostingSchedule::where('story_id', $story->id)->first();
            return view('user.story.single', compact('story', 'schedule'));
        } else {
            return redirect()->route('user.story.index');
        }
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $validation_rules = [
            'title' => 'required|max:255',
            'thumbnail_file' => 'mimes:jpg,jpeg,png,webp'
        ];
        $messages = [
            'title.required' => 'Nhập tên truyện',
            'title.max' => 'Tên truyện tối đa 255 ký tự',
            'thumbnail_file.mimes' => 'Chỉ upload file .jpg, png, jpeg, webp.'
        ];
        if ($id == 0) {
            $validation_rules['thumbnail_file'] = 'required|mimes:jpg,jpeg,png,webp';
            $messages['thumbnail_file.required'] = 'Vui lòng chọn ảnh bìa truyện!';
        }
        $validator = Validator::make($request->all(), $validation_rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = Auth::user();
        $slug = Str::slug($request->title);

        $oldThumbnail = '';
        if ($id > 0) {
            $story = Story::find($id);
            if ($story) {
                if (mb_strtoupper($request->title) == mb_strtoupper($story->name)) {
                    $slug = $story->slug;
                }
                $oldThumbnail = $story->thumbnail;
                if ($story->user_id != $user->id) {
                    return redirect()->back()->withInput();
                }
            } else {
                return redirect()->route('user.story.index');
            }
        }

        $checkSlug = Story::where('slug', $slug)
            ->where('id', '<>', $id)
            ->first();
        if ($checkSlug) {
            $slug = Helpers::generateRandomSlug($request->title);
        }

        $content = preg_replace("/<img[^>]+\>/i", "", $request->input('content'));
        $content = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $content);
        $content = str_replace("<div>", "<p>", $content);
        $content = str_replace("</div>", "</p>", $content);
        $content = str_replace("<h1>", "<p>", $content);
        $content = str_replace("</h1>", "</p>", $content);
        $content = str_replace("<h2>", "<p>", $content);
        $content = str_replace("</h2>", "</p>", $content);
        $content = str_replace("<h3>", "<p>", $content);
        $content = str_replace("</h3>", "</p>", $content);
        $content = str_replace("<h4>", "<p>", $content);
        $content = str_replace("</h4>", "</p>", $content);
        $content = str_replace("<h5>", "<p>", $content);
        $content = str_replace("</h5>", "</p>", $content);
        $content = str_replace("<h6>", "<p>", $content);
        $content = str_replace("</h6>", "</p>", $content);
        $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
        $content = htmlspecialchars($content);

        $is_full = 0;
        if ($request->is_full) {
            $is_full = $request->is_full;
        }

        $creative = 0;
        if ($request->creative) {
            $creative = $request->creative;
        }

        $year = date('Y');
        $month = date('m');
        $date = date('d');
        $path_img = $year . '/' . $month . '/' . $date . '/';

        //xử lý thumbnail
        $name_field = "thumbnail_file";
        if ($request->thumbnail_file) {
            $file = $request->file($name_field);
            $name = time() . '-' . $slug . '.jpg';
            $name = str_replace(' ', '-', $name);
            $url_folder_upload = "/images/story/" . $path_img;
            $file->move(public_path() . $url_folder_upload, $name);

            // Chèn logo vào ảnh bìa
            $manager = new ImageManager(new Driver());
            $logo = $manager->read(public_path('images/dammy-watermark.png'));
            $image = $manager->read(public_path($url_folder_upload . $name));

            // Logo tối đa chiếm 35% chiều rộng
            $maxWidth = $image->width() * 0.35;
            $logo->scale(width: $maxWidth);

            // Tính vị trí để logo nằm chính giữa ảnh gốc
            $centerX = intval(($image->width() - $logo->width()) / 2);
            $centerY = intval(($image->height() - $logo->height()) / 1.6);

            // Chèn logo vào giữa
            $image->place($logo, 'top-left', $centerX, $centerY);

            // Lưu lại hoặc trả ảnh về trình duyệt
            $path = public_path($url_folder_upload . $name);
            $image->save($path);

            $thumbnail = $path_img . $name;

            $path = 'images/story';
            Helpers::getThumbnail($path, $thumbnail, 230, $height = null);

            if ($oldThumbnail != '') {
                $delete_path = $_SERVER['DOCUMENT_ROOT'] . '/images/story/' . $oldThumbnail;
                if (file_exists($delete_path)) {
                    unlink($delete_path);
                }

                $delete_path = $_SERVER['DOCUMENT_ROOT'] . '/images/story/thumbs/230/' . $oldThumbnail;
                if (file_exists($delete_path)) {
                    unlink($delete_path);
                }
            }
        } elseif (isset($request->thumbnail_file_link) && $request->thumbnail_file_link != "") {
            $thumbnail = $request->thumbnail_file_link;
        } else {
            $thumbnail = "";
        }

        $category_item = ($request->category_item) ? $request->category_item : [];
        $data = [
            'name' => $request->title,
            'slug' => $slug,
            'search_name' => str_replace('-', '', $slug),
            'content' => $content,
            'thumbnail' => $thumbnail,
            'categories' => json_encode($category_item),
            'is_full' => $is_full,
            'creative' => $creative,
            'author' => $request->author,
            'type' => 0
        ];

        $model = 'Truyện';
        if ($id == 0) {
            $data['user_id'] = $user->id;

            if ($request->schedule_time != '' && time() < strtotime(strip_tags($request->schedule_time) . ':00')) {
                $schedule_time = strip_tags($request->schedule_time) . ':00';
                $schedule_time = Carbon::createFromFormat('d-m-Y H:i:s', $schedule_time)->format('Y-m-d H:i:s');
                $data['status'] = BaseConstants::INACTIVE;
            } else {
                $data['status'] = BaseConstants::ACTIVE;
            }

            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $story = Story::create($data);
            if ($story) {
                if ($data['status'] == BaseConstants::INACTIVE) {
                    StoryPostingSchedule::create([
                        'story_id' => $story->id,
                        'time' => $schedule_time,
                    ]);
                }
                $msg = trans('messages.create_msg', ['model' => $model]);
                $id = $story->id;

                StoryView::create(
                    [
                        'story_id' => $id,
                        'day' => 0,
                        'week' => 0,
                        'month' => 0,
                        'year' => 0,
                        'alltime' => 0
                    ]
                );

                //Noti to user follow
                $importJob = new PushNotificationNewStories($story, $user);
                dispatch($importJob);

                return redirect()->route('user.chapter.create', $id)->with('success_msg', $msg);
            }
        } else {
            if ($request->schedule_time != '' && time() < strtotime(strip_tags($request->schedule_time) . ':00')) {
                $schedule_time = strip_tags($request->schedule_time) . ':00';
                $schedule_time = Carbon::createFromFormat('d-m-Y H:i:s', $schedule_time)->format('Y-m-d H:i:s');
                StoryPostingSchedule::updateOrCreate(
                    ['story_id' => $id],
                    ['time' => $schedule_time]
                );
                $data['status'] = BaseConstants::INACTIVE;
            } elseif ($request->schedule_time != '' && time() >= strtotime(strip_tags($request->schedule_time) . ':00')) {
                $data['status'] = BaseConstants::ACTIVE;
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['updated_at'] = date('Y-m-d H:i:s');
                StoryPostingSchedule::where('story_id', $id)->delete();
            }

            Story::where("id", $id)
                ->where('user_id', $user->id)
                ->update($data);
            $msg = trans('messages.update_msg', ['model' => $model]);
        }
        Cache::pull('story_' . $slug);
        Cache::tags(['user_story_list_' . $user->id])->flush();

        return redirect()->route('user.story.detail', $id)->with('success_msg', $msg);
    }

    public function comments($story_id, Request $request)
    {
        $user_id = Auth::user()->id;
        $query = CommentStory::with(['user'])
            ->whereHas('story.user', function ($q) use ($user_id) {
                $q->where('id', $user_id);
            })
            ->where('story_id', $story_id)
            ->orderBy('updated_at', 'DESC');
        if ($request->search != '') {
            $query->where('content', 'LIKE', '%' . $request->search . '%');
        }

        $comments = $query->paginate(50);

        return view('user.story.comment', compact('comments', 'story_id'));
    }

    public function deleteComment($story_id, $cmt_id)
    {
        $user_id = Auth::user()->id;
        $result = CommentStory::where('story_id', $story_id)
            ->whereHas('story.user', function ($q) use ($user_id) {
                $q->where('id', $user_id);
            })
            ->where('id', $cmt_id)
            ->delete();
        if ($result) {
            return response()->json(
                [
                    'success' => true
                ]
            );
        }

        return response()->json(
            [
                'success' => false,
                'message' => 'Đã xãy ra lỗi trong quá trình xoá bình luận.'
            ]
        );
    }

    public function delete(Request $request)
    {
        $story_id = $request->story_id;
        $user_id = Auth::user()->id;

        $story = Story::where('id', $story_id)
            ->whereHas('user', function ($query) use ($user_id) {
                $query->where('id', $user_id);
            })
            ->first();
        if ($story) {
            //xóa thumbnail
            $url_upload = $_SERVER['DOCUMENT_ROOT'] . '/images/story/';
            $img = $story->thumbnail;
            if ($img != '') {
                $pt = $url_upload . $img;
                if (file_exists($pt)) {
                    unlink($pt);
                }
            }

            //xoá hình ảnh chapter
            $url_upload = $_SERVER['DOCUMENT_ROOT'];
            $chapters = Chapter::where('story_id', $story_id)->get();
            foreach ($chapters as $chapter) {
                $checkImages = ($chapter->content_images != '') ? json_decode($chapter->content_images) : [];
                if (count($checkImages)) {
                    foreach ($checkImages as $check) {
                        $delete_path = $url_upload . $check;
                        if (file_exists($delete_path)) {
                            unlink($delete_path);
                        }
                    }
                }
            }

            Chapter::where('story_id', $story_id)->delete();
            CommentStory::where('story_id', $story_id)->delete();
            StoryView::where('story_id', $story_id)->delete();
            Bookmark::where('story_id', $story_id)->delete();
            Donate::where('story_id', $story_id)->delete();
            Report::where('story_id', $story_id)->delete();
            UserChapter::where('story_id', $story_id)->delete();
            StoryPostingSchedule::where('story_id', $story_id)->delete();
            Story::where('id', $story_id)->delete();
        }

        return response()->json(
            [
                'success' => true,
                'message' => 'Đã xoá ' . $request->title . '.'
            ]
        );
    }

    public function quickUpdate(Request $request)
    {
        $story_id = $request->story_id;
        $type = $request->type;
        $user_id = Auth::user()->id;
        $value = (int)$request->value;
        switch ($type) {
            case 'is_full':
                Story::where('id', $story_id)
                    ->where('user_id', $user_id)
                    ->update(['is_full' => $value]);
                break;
            case 'creative':
                Story::where('id', $story_id)
                    ->where('user_id', $user_id)
                    ->update(['creative' => $value]);
                break;
            default:
                Story::where('id', $story_id)
                    ->where('user_id', $user_id)
                    ->update(['warning' => $value]);
                break;
        }

        return response()->json(
            [
                'success' => true,
            ]
        );
    }

    public function registerRecommendedStory()
    {
        $today = Carbon::today();
        $tomorrow = $today->addDay();
        $recommendedStory = RecommendedStory::where('date', $tomorrow->toDateString())
            ->first();
        if ($recommendedStory) {
            $group_data = json_decode($recommendedStory->group_data, true);
            $stories = [];

            foreach ($group_data as $item) {
                $story = Story::with(['user'])->where('id', $item['story_id'])->first();
                if ($story) {
                    $stories[] = $story;
                }
            }
            $recommendedStory->stories = $stories;
        }

        $userStories = Story::where('user_id', Auth::user()->id)
            ->where('status', BaseConstants::ACTIVE)
            ->orderBy('created_at', 'DESC')
            ->get();

        $tomorrowDateString = $tomorrow->toDateString();
        return view('user.story.recommended-story', compact('recommendedStory', 'userStories', 'tomorrowDateString'));
    }

    public function registerRecommendedStoryProcess(Request $request)
    {
        //Kiểm tra khung giờ có thể đăng ký: từ 18h đến 24h
        $time = date("H:i:s");
        $expTime = explode(':', $time);
        $hours = $expTime[0];
        if ((int)$hours < 18) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Chỉ có thể đăng ký truyện đề cử trong khung giờ 18h đến 24h.'
                ]
            );
        } else {
            $user_id = Auth::user()->id;
            $importJob = new RegisterRecommendedStory($request->story_id, $user_id);
            dispatch($importJob);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Yêu cầu đăng ký của bạn đã được đưa vào hàng đợi, kết quả đăng ký sẽ được hiển thị trong 1-2 phút.'
                ]
            );
        }
    }

    public function recommendedMyStories()
    {
        $recommended_my_stories = Auth::user()->recommended_my_stories;
        $myStories = ($recommended_my_stories != '') ? json_decode($recommended_my_stories, true) : [];

        $userStories = Story::where('user_id', Auth::user()->id)
            ->where('status', BaseConstants::ACTIVE)
            ->orderBy('created_at', 'DESC')
            ->get();

        $recommendedStory = Story::where('user_id', Auth::user()->id)
            ->where('status', BaseConstants::ACTIVE)
            ->whereIn('id', $myStories)
            ->orderBy('total_view', 'DESC')
            ->get();

        return view('user.story.recommended-my-story', compact('recommendedStory', 'userStories', 'myStories'));
    }

    public function processRecommendedMyStories(Request $request)
    {
        User::where('id', Auth::user()->id)
            ->update(['recommended_my_stories' => json_encode($request->story_ids)]);
        Cache::pull('recommendedMyStory_' . Auth::user()->id);
        Cache::pull('author_' . Auth::user()->id);
        return response()->json(
            [
                'success' => true
            ]
        );
    }
}
