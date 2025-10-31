<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Category;
use App\Models\Join_Category_Post;
use Illuminate\Support\Facades\Hash;
use App\Libraries\Helpers;
use Illuminate\Support\Str;
use DB;
use File;
use Image;
use Config;
use Illuminate\Pagination\Paginator;

class ModuleController extends Controller
{
    public $type_module = 'module';
    public $folder = 'module';

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

    public function listItem()
    {
        $id_category = 0;
        $module = Module::orderBy('stt', 'asc')->get();

        return view('admin.module.index')->with(['module' => $module]);
    }


    public function addItem()
    {
        return view('admin.module.add');
    }

    public function itemView($id)
    {
        $module = Module::find($id);
        return view('admin.module.single', ['module'=>$module]);
    }

    public function postItem(Request $rq)
    {
        //id post
        $id_edit = $rq->sid;

        $title = $rq->title;
        $title_slug = addslashes($title);
        if(empty($title_slug) || $title_slug == ''):
           $title_slug = Str::slug($title_new);
        endif;

        $alias = $rq->alias;
        $type = $rq->type;
        $stt = $rq->stt;
        $hide = $rq->hide;

        //xử lý thumbnail
        $picture = "";
        $image = new Image();
        $name_field = "thumbnail_file";
        $datetime_now=date('Y-m-d H:i:s');
        $datetime_convert=strtotime($datetime_now);

        if($rq->thumbnail_file):
            $file = $rq->file($name_field);
            $timestamp = $datetime_convert;
            $name = $timestamp. '-brand-' .$file->getClientOriginalName();
            $picture = $name;
            $image->filePath = $name;
            $url_folder_upload = "/images/".$this->folder."/";
            $file->move(public_path().$url_folder_upload,$name);
        else:
           if(isset($rq->thumbnail_file_link) && $rq->thumbnail_file_link !=""):
               $picture = $rq->thumbnail_file_link;
           else:
               $picture = "";
           endif;
        endif;

        if($rq->picture_file):
            $file = $rq->file('picture_file');
            $timestamp = $datetime_convert;
            $picture_bottom = $timestamp. '-brand-bottom-' .$file->getClientOriginalName();
            $image->filePath = $picture_bottom;
            $url_folder_upload = "/images/".$this->folder."/";
            $file->move(public_path().$url_folder_upload,$picture_bottom);
        else:
           if(isset($rq->thumbnail_file_link) && $rq->thumbnail_file_link !=""):
               $picture_bottom = $rq->thumbnail_file_link;
           else:
               $picture_bottom = "";
           endif;
        endif;


        if ($id_edit > 0) {
            $data = Module::find($id_edit);

            $data->title = $title;

            $data->slug = $title_slug;

            $data->code = $alias;
            $data->type = $type;
            $data->stt = $stt;
            $data->hide = $hide;

            $data->picture_bottom = $picture_bottom;
            $data->picture = $picture;

            $data->save();
            return redirect('admin/module/list')->with('thongbao', 'sửa thành công');
        } else {
            $data = new Module;

            $data->title = $title;
            $data->slug = $title_slug;
            $data->code = $alias;
            $data->type = $type;
            $data->stt = $stt;
            $data->hide = $hide;
            $data->picture_bottom = $picture_bottom;
            $data->picture = $picture;

            $data->save();
            return redirect('admin/module/list')->with('thongbao', 'sửa thành công');
        }
    }

    public function itemDel($id)
    {
        $fetch = Module::where('id', $id)->first();
        $fetch->delete();

        return redirect('admin/'.$this->type_module.'/list')->with('thongbao', 'Xóa thành công');
    }
}
