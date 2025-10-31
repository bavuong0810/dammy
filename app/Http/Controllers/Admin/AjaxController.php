<?php

namespace App\Http\Controllers\Admin;

use App\Constants\BaseConstants;
use App\Models\Chapter;
use App\Models\CommentStory;
use App\Models\Donate;
use App\Models\Module;
use App\Models\Report;
use App\Models\Role;
use App\Models\RoleDetail;
use App\Models\Story;
use App\Models\StoryView;
use App\Models\UserChapter;
use App\Tasks\Admin\RoleTask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Category;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Artisan;

class AjaxController extends Controller
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

    public function ajax_delete(Request $rq){
        $type = $rq->type;
        $check_data = $rq->seq_list;
        $arr = array();
        $values = "";
        for($i=0;$i<count($check_data);$i++):
            $values .= (int)$check_data[$i].",";
            $arr[]=(int)$check_data[$i];
        endfor;
        $user_role = $rq->user_role;
        $user_role_id = $rq->user_role['role_id'];
        $is_super_admin = ($user_role_id == BaseConstants::SUPER_ADMIN_ROLE_ID) ? true : false;
        switch ($type) {
            case 'module':
                $deletePermission = app(RoleTask::class)
                    ->checkPermission('super-admin', [BaseConstants::DELETE_PERMISSION], $user_role);
                if ($deletePermission) {
                    Module::whereIn('id', $arr)->delete();
                    return 1;
                } else {
                    return 0;
                }
                break;
            case 'role':
                $deletePermission = app(RoleTask::class)
                    ->checkPermission('super-admin', [BaseConstants::DELETE_PERMISSION], $user_role);
                if ($deletePermission) {
                    Role::whereIn('id', $arr)->delete();
                    RoleDetail::whereIn('role_id', $arr)->delete();
                    return 1;
                } else {
                    return 0;
                }
                break;

            case 'page':
                $deletePermission = app(RoleTask::class)
                    ->checkPermission('page-management', [BaseConstants::DELETE_PERMISSION], $user_role);
                if ($deletePermission) {
                    //xóa thumbnail
                    $url_upload = $_SERVER['DOCUMENT_ROOT'] . '/images/page/';
                    $data_page = Page::whereIn('id', $arr)->get();
                    foreach ($data_page as $row) {
                        $img = $row->thumbnail;
                        if ($img != '') {
                            $pt = $url_upload . $img;
                            if (file_exists($pt)) {
                                unlink($pt);
                            }
                        }
                    }
                    Page::whereIn('id', $arr)->delete();
                    return 1;
                } else {
                    return 0;
                }
                break;
            case 'category':
                $deletePermission = app(RoleTask::class)
                    ->checkPermission('category-management', [BaseConstants::DELETE_PERMISSION], $user_role);
                if ($deletePermission) {
                    Category::whereIn('id', $arr)->delete();
                    return 1;
                } else {
                    return 0;
                }
                break;
            case 'story':
                $deletePermission = app(RoleTask::class)
                    ->checkPermission('story-management', [BaseConstants::DELETE_PERMISSION], $user_role);
                if ($deletePermission) {
                    foreach ($arr as $it) {
                        $story = Story::where('id', $it)->first();
                        if ($story) {
                            Chapter::where('story_id', $story->id)->delete();
                            StoryView::where('story_id', $story->id)->delete();
                            Bookmark::where('story_id', $story->id)->delete();
                            Donate::where('story_id', $story->id)->delete();
                            Report::where('story_id', $story->id)->delete();
                            CommentStory::where('story_id', $story->id)->delete();
                            UserChapter::where('story_id', $story->id)->delete();
                            Story::where('id', $story->id)->delete();
                        } else {
                            return 0;
                        }
                    }
                    return 1;
                } else {
                    return 0;
                }
                break;
            case 'chapter':
                $deletePermission = app(RoleTask::class)
                    ->checkPermission('story-management', [BaseConstants::DELETE_PERMISSION], $user_role);
                if ($deletePermission) {
                    foreach ($arr as $it) {
                        $chapter = Chapter::where('id', $it)->first();
                        if ($chapter) {
                            $story_id = $chapter->story_id;
                            //delete image chapter
                            $url_upload = $_SERVER['DOCUMENT_ROOT'] . '/images/chapter/';
                            $imgPcDarkMode = $story_id . '/' . $it . '-pc-dark-mode.jpg';
                            if ($imgPcDarkMode != '') {
                                $pt = $url_upload . $imgPcDarkMode;
                                if (file_exists($pt)) {
                                    unlink($pt);
                                }
                            }
                            $imgPc = $story_id . '/' . $it . '-pc.jpg';
                            if ($imgPc != '') {
                                $pt = $url_upload . $imgPc;
                                if (file_exists($pt)) {
                                    unlink($pt);
                                }
                            }
                            $imgMobileDarkMode = $story_id . '/' . $it . '-mobile-dark-mode.jpg';
                            if ($imgMobileDarkMode != '') {
                                $pt = $url_upload . $imgMobileDarkMode;
                                if (file_exists($pt)) {
                                    unlink($pt);
                                }
                            }
                            $imgMobile = $story_id . '/' . $it . '-mobile.jpg';
                            if ($imgMobile != '') {
                                $pt = $url_upload . $imgMobile;
                                if (file_exists($pt)) {
                                    unlink($pt);
                                }
                            }

                            Chapter::where('id', $it)->delete();
                            CommentStory::where('chapter_id', $it)->delete();

                            $totalChapter = Chapter::where('story_id', $story_id)->count();
                            Story::where('id', $story_id)->update(['total_chapter' => $totalChapter]);
                        } else {
                            return 0;
                        }
                    }
                    return 1;
                } else {
                    return 0;
                }
                break;
            default:
                break;
        }
    }
}
