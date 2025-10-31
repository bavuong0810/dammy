<?php

namespace App\Http\Controllers\Admin;

use App\Constants\BaseConstants;
use App\Exports\ExportStory;
use App\Libraries\Helpers;
use App\Models\Chapter;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class StoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
    }

    public function index(Request $request)
    {
        $query = Story::select('id', 'name', 'slug', 'thumbnail', 'created_at', 'status', 'audio')
            ->orderBy('created_at', 'DESC');

        if (isset($request->search_title) && $request->search_title != '') {
            $query->where('name', 'LIKE', '%' . $request->search_title . '%');
        }

        if (isset($request->category) && $request->category != '') {
            $query->whereJsonContains('categories', (string)$request->category);
        }

        if ($request->story_type != '') {
            $query->where('proposed', BaseConstants::ACTIVE);
        }

        $translateTeams = User::where('active', BaseConstants::ACTIVE)
            ->where('type', User::UserType['TranslateTeam'])
            ->orderBy('name', 'ASC')
            ->get();

        $totalStories = Story::where('status', BaseConstants::ACTIVE)->count();
        $list = $query->paginate(50);
        return view('admin.story.index', compact('list', 'translateTeams', 'totalStories'));
    }

    public function detail(Request $request, $id)
    {
        $story = Story::where('id', $id)->first();
        if ($story) {
            return view('admin.story.single', compact('story'));
        } else {
            return redirect()->route('admin.story.index');
        }
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $story = Story::find($id);
        if (!$story) {
            return redirect()->back();
        }
        $oldCover = $story->cover_image;
        $title_new = $request->post_title;
        $slug = Str::slug($title_new);

        $content = preg_replace("/<img[^>]+\>/i", "", $request->input('content'));
        $content = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $content);
        $content = str_replace("<div>", "<p>", $content);
        $content = str_replace("</div>", "</p>", $content);
        $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
        $content = htmlspecialchars($content);

        $oldThumbnail = $story->thumbnail;

        //check story full
        $is_full = 0;
        if ($request->is_full) {
            $is_full = $request->is_full;
        }

        //proposed
        $proposed = 0;
        if ($request->proposed) {
            $proposed = $request->proposed;
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
            $thumbnail = $path_img . $name;

            $path = 'images/story';
            Helpers::getThumbnail($path, $thumbnail, 230, $height = null);

            if ($oldThumbnail != '') {
                $delete_path = $_SERVER['DOCUMENT_ROOT'] . '/images/story/' . $oldThumbnail;
                if (file_exists($delete_path)) {
                    unlink($delete_path);
                }
            }
        } elseif (isset($request->thumbnail_file_link) && $request->thumbnail_file_link != "") {
            $thumbnail = $request->thumbnail_file_link;
        } else {
            $thumbnail = "";
        }

        $data = [
            'name' => $title_new,
            'slug' => $slug,
            'content' => $content,
            'thumbnail' => $thumbnail,
            'categories' => json_encode($request->category_item),
            'is_full' => $is_full,
            'another_name' => $request->another_title,
            'proposed' => $proposed
        ];

        $year = date('Y');
        $month = date('m');
        $date = date('d');
        $path_img = $year . '/' . $month . '/' . $date . '/';

        if ($request->cover_image) {
            $file = $request->file('cover_image');
            $filename = time() . '-' . $file->getClientOriginalName();
            $filename = str_replace(' ', '', $filename);
            $folder_upload = "/images/story/" . $path_img;
            $file->move(public_path() . $folder_upload, $filename);
            $cover_image = $folder_upload . $filename;
            $data['cover_image'] = $cover_image;

            //Delete old cover image
            if ($oldCover != '') {
                $url_upload = $_SERVER['DOCUMENT_ROOT'];
                $delete_path = $url_upload . $oldCover;
                if (file_exists($delete_path)) {
                    unlink($delete_path);
                }
            }
        }

        Story::where ("id", $id)->update($data);
        $msg = trans('messages.update_msg', ['model' => 'Truyện']);
        return redirect()->route('admin.story.detail', $id)->with('success_msg', $msg);
    }

    public function transfer(Request $request)
    {
        $transfer_to_user = $request->transfer_user;
        $id = $request->comics_transfer_id;
        $user = User::where('id', $transfer_to_user)
            ->where('type', User::UserType['TranslateTeam'])
            ->first();
        $story = Story::find($id);
        if ($story && $user) {
            Story::where('id', $id)->update(['user_id' => $transfer_to_user]);
            Chapter::where('story_id', $id)->update(['user_id' => $transfer_to_user]);

            return redirect()->route('admin.story.index')->with('success_msg', 'Đã chuyển giao truyện ' . $story->name . ' cho người dùng ' . $user->name);
        } else {
            return redirect()->route('admin.story.index')->withErrors('Không tìm thấy truyện hoặc người dùng này.');
        }
    }

    public function hide(Request $request)
    {
        $story_id = $request->story_id;
        $status = (int)$request->status;
        $newStatus = BaseConstants::ACTIVE;
        $message = 'Đã hiển thị truyện';
        if ($status == BaseConstants::ACTIVE) {
            $newStatus = BaseConstants::INACTIVE;
            $message = 'Đã ẩn truyện';
        }

        Story::where('id', $story_id)->update(
            [
                'status' => $newStatus
            ]
        );

        return response()->json(
            [
                'success' => true,
                'message' => $message
            ]
        );
    }

    public function export()
    {
        $stories = Story::with(['user'])
            ->orderBy('user_id', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->get();
        $rows = [];
        foreach ($stories as $item) {
            $totalChapter = Chapter::where('story_id', $item->id)
                ->count();
            $row = [
                'id' => $item->id,
                'name' => $item->name,
                'total_chapter' => $totalChapter,
                'last_update' => $item->updated_at,
                'is_full' => ($item->is_full) ? 'Hoàn thành' : 'Đang phát hành',
                'link' => route('admin.story.detail', $item->id),
                'team' => $item->user->name,
                'thumbnail' => asset('images/story/' . $item->thumbnail),
                'status' => ''
            ];
            $rows[] = $row;
        }
        return (new ExportStory($rows))->download('Export Story ' . date('d-m-Y') . '.xlsx');
    }
}
