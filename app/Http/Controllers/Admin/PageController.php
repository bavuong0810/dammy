<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Support\Facades\Hash;
use App\Libraries\Helpers;
use Illuminate\Support\Str;
use DB, File, Image;

class PageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function listPage()
    {
        $data_page = Page::get();
        return view('admin.page.index', compact('data_page'));
    }

    public function createPage()
    {
        return view('admin.page.single');
    }

    public function pageDetail($id)
    {
        $detail = Page::where('id', $id)->first();
        if ($detail) {
            return view('admin.page.single', compact('detail'));
        } else {
            abort(404);
        }
    }

    public function storePage(Request $request)
    {
        $id = $request->id;
        $title_new = $request->post_title;
        $description = htmlspecialchars($request->post_description);
        $content = htmlspecialchars($request->post_content);

        $template = 0;
        if ($request->template) {
            $template = (int)$request->template;
        }

        $data = array(
            'title' => $title_new,
            'slug' => Str::slug($request->post_title),
            'description' => $description,
            'content' => $content,
            'template' => $template,
            'status' => $request->status,
        );

        if ($id == 0) {
            $response = Page::create($data);
            $id_insert = $response->id;
            if ($id_insert > 0):
                $msg = "Page has been registered";
                $url = route('admin.pages');
                Helpers::msg_move_page($msg, $url);
            endif;
        } else {
            $response = Page::where("id", $id)->update($data);
            $msg = "Page has been Updated";
            $url = route('admin.pageDetail', $id);
            Helpers::msg_move_page($msg, $url);
        }

    }
}
