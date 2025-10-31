<?php

namespace App\Http\Controllers\Admin;

use App\Models\Story;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ChapterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index($story_id){
        $list_chapter = Chapter::select('id', 'name', 'slug', 'vol_number', 'view', 'created_at', 'story_id')
            ->where('story_id', $story_id)
            ->orderBy('vol_number', 'DESC')
            ->get();
        return view('admin.chapter.index', compact('list_chapter', 'story_id'));
    }

    public function create($story_id){
        return view('admin.chapter.single', compact('story_id'));
    }

    public function detail(Request $request, $story_id, $id){

        $chapter = Chapter::where('id', $id)
            ->where('story_id', $story_id)
            ->first();
        if($chapter){
            return view('admin.chapter.single', compact('chapter'));
        } else{
            return redirect()->route('admin.chapter.index', $story_id);
        }
    }

    public function store(Request $request){
        $title = $request->post_title;
        $slug = Str::slug(str_replace('.', '-', $title));

        $story = Story::where('id', $request->story_id)
            ->first();

        $vol_number = $request->vol_number;
        $content = '';
        if ($story->type == 0) {
            $content = preg_replace("/<img[^>]+\>/i", "", $request->input('content'));
            $content = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $content);
            $content = str_replace("<div>", "<p>", $content);
            $content = str_replace("</div>", "</p>", $content);
            $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
            $content = htmlspecialchars($content);
        }

        $data = [
            'story_id' => $request->story_id,
            'name' => $title,
            'slug' => $slug,
            'vol_number' => $vol_number,
            'content' => $content,
        ];

        $id = $request->id;

        Chapter::where("id", $id)->update($data);
        Cache::pull($story->slug . '_chapter_' . $slug);
        $msg = trans('messages.update_msg', ['model' => 'Chương']);
        return redirect()->route('admin.chapter.detail', [$request->story_id, $id])->with('success_msg', $msg);
    }
}
