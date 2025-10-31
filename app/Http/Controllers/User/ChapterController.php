<?php

namespace App\Http\Controllers\User;

use App\Constants\BaseConstants;
use App\Jobs\CompressionImage;
use App\Jobs\PushNotificationNewChapter;
use App\Libraries\Helpers;
use App\Models\Story;
use App\Tasks\TelegramTask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ChapterController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request, $story_id) {
        $user_id = Auth::user()->id;
        $story = Story::find($story_id);
        $query = Chapter::where('story_id', $story_id)
            ->whereHas('story', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })
            ->orderBy('created_at', 'DESC');

        if ($request->search != '') {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $chapters = $query->paginate(24);
        return view('user.chapter.index', compact('chapters', 'story'));
    }

    public function create($story_id) {
        $user_id = Auth::user()->id;
        $story = Story::where('id', $story_id)
            ->where('user_id', $user_id)
            ->first();
        if ($story) {
            $list_chapters = Chapter::where('story_id', $story_id)
                ->where('status', BaseConstants::ACTIVE)
                ->orderBy('created_at', 'DESC')
                ->select(
                    'id',
                    'name',
                    'slug'
                )
                ->get();

            $last_chapter = Chapter::where('story_id', $story_id)
                ->where('status', BaseConstants::ACTIVE)
                ->orderBy('created_at', 'DESC')
                ->select(
                    'id',
                    'name',
                    'slug'
                )
                ->first();

            //process get chapter next, prev
            $next_chapter = '';
            $prev_chapter = '';
            if ($last_chapter) {
                $prev_chapter = route('user.chapter.detail', [$story_id, $last_chapter->id]);
            }
            return view(
                'user.chapter.single',
                compact('story_id', 'story', 'next_chapter', 'prev_chapter', 'last_chapter', 'list_chapters')
            );
        } else {
            return redirect()->route('user.story.index');
        }
    }

    public function detail($story_id, $id) {
        $user_id = Auth::user()->id;
        $chapter = Chapter::where('id', $id)
            ->whereHas('story', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })
            ->where('story_id', $story_id)
            ->first();
        if ($chapter) {
            $story = Story::where('id', $story_id)
                ->where('user_id', $user_id)
                ->first();

            $list_chapters = Chapter::where('story_id', $story_id)
                ->where('status', BaseConstants::ACTIVE)
                ->orderBy('created_at', 'DESC')
                ->select(
                    'id',
                    'name',
                    'slug'
                )
                ->get();

            $next = 0;
            $prev = 0;
            if ($list_chapters && count($list_chapters) > 0) {
                foreach ($list_chapters as $key => $item) {
                    if ($item->id == $chapter->id && count($list_chapters) > 1) {
                        $next = $key - 1;
                        $prev = $key + 1;
                        break;
                    }
                }
            }

            $next_chapter = '';
            if ($next >= 0 && isset($list_chapters[$next])) {
                $next_chapter = route('user.chapter.detail', [$story_id, $list_chapters[$next]->id]);
            }
            $prev_chapter = '';
            if ($prev >= 0 && isset($list_chapters[$prev])) {
                $prev_chapter = route('user.chapter.detail', [$story_id, $list_chapters[$prev]->id]);
            }

            $last_chapter = (isset($list_chapters[$prev])) ? $list_chapters[$prev] : null;

            return view('user.chapter.single', compact('chapter', 'story_id', 'story', 'next_chapter', 'prev_chapter', 'last_chapter', 'list_chapters'));
        } else {
            return redirect()->route('user.chapter.index', $story_id);
        }
    }

    public function store($story_id, Request $request)
    {
        $validation_rules = [
            'title' => 'required|max:255',
        ];
        $messages = [
            'title.required' => 'Nhập tiêu đề chương',
            'title.max' => 'Tiêu đề chương tối đa 255 ký tự',
        ];
        $validator = Validator::make($request->all(), $validation_rules, $messages);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $id = $request->id;

        $user_id = Auth::user()->id;
        $story = Story::where('id', $story_id)
            ->where('user_id', $user_id)
            ->first();
        if (!$story) {
            return redirect()->route('user.story.index');
        }

        //check auth
        if ($id > 0) {
            $chapter = Chapter::where('id', $id)
                ->whereHas('story', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                })
                ->where('story_id', $story_id)
                ->first();
            if (!$chapter) {
                return redirect()->route('user.story.index');
            }
        }

        $title = $request->title;
        $slug = Str::slug(str_replace('.', '-', $title));
//        $vol_number = $request->vol_number;

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
        $content = Helpers::replaceCharacterChapter($content);
        $content = htmlspecialchars($content);

        $status = (int)$request->status;
        $warning = (int)$request->warning;
        $data = [
            'story_id' => $story_id,
            'name' => $title,
            'slug' => $slug,
            'coin' => 0,
            'status' => $status,
            'warning' => $warning
        ];

        $data['content'] = $content;

        $model = 'Chương truyện';
        $story_update = [];
        if ($id == 0) {
            //tạo chương truyện
            $data['user_id'] = Auth::user()->id;
            $data['view'] = 0;
            $data['processing'] = BaseConstants::ACTIVE;

            $chapter = Chapter::create($data);
            if ($chapter) {
                //push notification to followers
                if ($story->is_full == BaseConstants::INACTIVE) {
                    $importJob = new PushNotificationNewChapter($story, $chapter);
                    dispatch($importJob);
                }

                $msg = trans('messages.create_msg', ['model' => $model]);

                $totalChapter = Chapter::where('story_id', $story_id)->count();
                $story_update['updated_at'] = date('Y-m-d H:i:s');
                $story_update['total_chapter'] = $totalChapter;
            } else {
                return redirect()->back()->withInput()->withErrors('Đã xãy ra lỗi, vui lòng thử lại.');
            }
        } else {
            Chapter::where("id", $id)->update($data);
            $msg = trans('messages.update_msg', ['model' => $model]);
        }

        $newChapter = Chapter::where('story_id', $story_id)
            ->orderBy('created_at', 'DESC')
            ->first();
        if ($newChapter) {
            $newChapterTitle = $newChapter->name;
            $story_update['last_chapter'] = $newChapterTitle;
        }
        Story::where('id', $story_id)->update($story_update);

        /*
        // Các từ nhạy cảm cần kiểm tra
        $sensitive_words = json_decode(Helpers::get_option('sensitive-words'), true);

        // Tạo biểu thức chính quy từ danh sách các từ nhạy cảm
        $pattern = "/" . implode("|", array_map('preg_quote', $sensitive_words)) . "/i";

        // Kiểm tra sự xuất hiện của các từ nhạy cảm trong đoạn văn bản
        if (preg_match($pattern, $checkContent, $matches)) {
            if (env('APP_ENV') == 'production') {
                $chapter_link = route('chapter.detail', [$story->slug, $chapter->slug]);
                $admin_link = route('admin.chapter.detail', [$story->id, $chapter->id]);
                $text = "<b>[" . Helpers::get_setting('company_name') . "]</b> Nội dung chương chứa từ nhạy cảm\n"
                    . "<b>Từ nhạy cảm: </b> $matches[0] \n"
                    . "<b>Link chapter: </b> $chapter_link \n"
                    . "<b>Link Admin: </b> $admin_link \n";
                $this->telegramTask->sendMessage($text, true);
            }
        }
        */

        if ($id > 0) {
            Cache::pull($story->slug . '_chapter_' . $chapter->slug);
            return redirect()->route('user.chapter.detail', [$story_id, $id])->with('success_msg', $msg);
        } else {
            Cache::pull('list_chapters_story_id_' . $story_id);
            Cache::pull('pageStory_page_1');
            Cache::pull('pageStory_page_2');
            Cache::pull('homepageContent');
            Cache::pull('authorPageContent_' . $user_id . '_1');
            Cache::pull('authorPageContent_' . $user_id . '_2');
            return redirect()->route('user.chapter.create', $story_id)->with('success_msg', $msg);
        }
    }

    public function bulkPosting($story_id)
    {
        $user_id = Auth::user()->id;
        $story = Story::where('id', $story_id)
            ->where('user_id', $user_id)
            ->first();
        if ($story) {
            return view('user.chapter.bulk-posting', compact('story', 'story_id'));
        } else {
            return redirect()->route('user.story.index');
        }
    }

    public function processBulkPosting($story_id, Request $request)
    {
        set_time_limit(0);
        $user_id = Auth::user()->id;
        $story = Story::where('id', $story_id)
            ->where('user_id', $user_id)
            ->first();
        if (!$story) {
            return redirect()->route('user.story.index');
        }

        // Chuỗi nội dung
        $content = strip_tags($request->input('content'));

        // Tách chuỗi thành mảng các chương và nội dung
        $chapters = preg_split('/(?=Chương \d+:)/', $content, -1, PREG_SPLIT_NO_EMPTY);

        $totalChapterCreated = 0;

        foreach ($chapters as $key => $chapter) {
            // Tìm vị trí của dấu xuống dòng đầu tiên trong mỗi chương
            $firstNewLinePosition = strpos($chapter, "\n");
            // Tách tiêu đề chương và nội dung.

            $title = substr($chapter, 0, $firstNewLinePosition);
            $title = str_replace(["\r", "\n"], '', $title);
            if (substr($title, -1) == ':') {
                $title = str_replace(':', '', $title);
            }
            if (substr($title, -2) == ': ') {
                $title = str_replace(': ', '', $title);
            }

            $content = substr($chapter, $firstNewLinePosition + 1);
            $content = $this->convertToParagraphs($content);
            $content = Helpers::replaceCharacterChapter($content);
            $content = htmlspecialchars($content);

            //Kiểm tra chương đã đăng chưa
            $checkChapter = Chapter::where('name', $title)
                ->where('story_id', $story_id)
                ->first();
            if ($checkChapter) {
                $checkChapter->content = $content;
                $checkChapter->save();
            } else {
                $slug = Str::slug(str_replace('.', '-', $title));
                $data = [
                    'story_id' => $story_id,
                    'name' => $title,
                    'slug' => $slug,
                    'coin' => 0,
                    'status' => BaseConstants::ACTIVE,
                    'word_count' => 0,
                    'user_id' => $user_id,
                    'view' => 0,
                    'processing' => BaseConstants::ACTIVE,
                    'content' => $content
                ];
                Chapter::create($data);
                sleep(1.2);
            }
            $totalChapterCreated++;
            if ($key + 1 == $totalChapterCreated) {
                $newChapter = Chapter::where('story_id', $story_id)
                    ->orderBy('created_at', 'DESC')
                    ->first();
                $totalChapter = Chapter::where('story_id', $story_id)->count();
                $story_update['updated_at'] = date('Y-m-d H:i:s');
                if ($newChapter) {
                    $newChapterTitle = $newChapter->name;
                    $story_update['last_chapter'] = $newChapterTitle;
                    $story_update['total_chapter'] = $totalChapter;
                }

                Story::where('id', $story_id)->update($story_update);
                Cache::pull('list_chapters_story_id_' . $story_id);
                Cache::pull('pageStory_page_1');
                Cache::pull('pageStory_page_2');
                Cache::pull('homepageContent');
                Cache::pull('authorPageContent_' . $user_id . '_1');
                Cache::pull('authorPageContent_' . $user_id . '_2');
            }
        }

        return redirect()->route('user.chapter.bulkPosting', $story_id)
            ->with('success_msg', 'Đã tạo thành công ' . $totalChapterCreated . ' chương.');
    }

    public function quickUpdate(Request $request)
    {
        $story_id = $request->story_id;
        $chapter_id = $request->chapter_id;
        $type = $request->type;
        $user_id = Auth::user()->id;
        $value = (int)$request->value;
        if ($type == 'warning') {
            Chapter::where('id', $chapter_id)
                ->where('story_id', $story_id)
                ->where('user_id', $user_id)
                ->update(['warning' => $value]);

            return response()->json(
                [
                    'success' => true,
                ]
            );
        }
        return response()->json(
            [
                'success' => false,
            ]
        );
    }

    private function convertToParagraphs($text) {
        // Chia đoạn văn bản thành các dòng dựa trên ký tự xuống dòng
        $lines = preg_split('/\r\n|\r|\n/', $text);

        // Lọc bỏ các dòng trống
        $lines = array_filter($lines, 'strlen');

        // Bao bọc mỗi dòng bằng thẻ <p> và nối lại thành một chuỗi duy nhất
        $paragraphs = array_map(function($line) {
            return '<p>' . htmlspecialchars($line) . '</p>';
        }, $lines);

        return implode("\n", $paragraphs);
    }
}
