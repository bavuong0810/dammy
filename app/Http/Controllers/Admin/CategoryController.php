<?php

namespace App\Http\Controllers\Admin;

use App\Constants\BaseConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Libraries\Helpers;
use Illuminate\Support\Str;

class CategoryController extends Controller
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

    public function index()
    {
        $list = Category::orderBy('created_at', 'DESC')
            ->get();
        return view('admin.category.index', compact('list'));
    }

    public function create()
    {
        return view('admin.category.single');
    }

    public function detail($id)
    {
        $detail = Category::where('id', $id)->first();
        if ($detail) {
            return view('admin.category.single', compact('detail'));
        } else {
            abort(404);
        }
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $title = $request->title;

        $data = [
            'name' => $title,
            'description' => htmlspecialchars($request->description),
            'slug' => Str::slug($title),
            'sort' => $request->cat_thutu,
            'status' => BaseConstants::ACTIVE
        ];

        if ($id > 0) {
            //update
            Category::where("id", $id)->update($data);
            $msg = "Danh mục truyện đã được cập nhật,";
            $url = route('admin.category.detail', $id);
            Helpers::msg_move_page($msg, $url);
        } else {
            // insert
            $response = Category::create($data);
            $id_insert = $response->id;
            if ($id_insert > 0):
                $msg = "Danh mục truyện đã được tạo.";
                $url = route('admin.category.detail', $id_insert);
                Helpers::msg_move_page($msg, $url);
            endif;
        }
    }
}
