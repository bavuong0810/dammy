<?php

namespace App\Http\Controllers;

use App\Constants\BaseConstants;
use App\Models\Page;
use App\Models\User;
use App\Models\Chapter;
use App\Models\Story;
use App\Models\Category;

class SitemapController extends Controller
{
    public function home()
    {
        $categories = Category::where('status', BaseConstants::ACTIVE)
            ->get();
        return response()->view('sitemap.index', compact('categories'))
            ->header('Content-Type', 'text/xml');
    }

    public function static()
    {
        return response()->view('sitemap.static')->header('Content-Type', 'text/xml');
    }

    public function pages()
    {
        $pages = Page::where('status', BaseConstants::ACTIVE)
            ->where('content', '!=', '')
            ->where('template', '!=', BaseConstants::ACTIVE)
            ->orderBy('updated_at', 'DESC')
            ->get();
        return response()->view('sitemap.page', compact('pages'))
            ->header('Content-Type', 'text/xml');
    }

    public function categories()
    {
        $categories = Category::where('status', BaseConstants::ACTIVE)->get();
        if ($categories) {
            return response()->view('sitemap.category', compact('categories'))
                ->header('Content-Type', 'text/xml');
        } else {
            abort(404);
        }
    }

    public function stories()
    {
        $stories = Story::where('status', BaseConstants::ACTIVE)
            ->whereHas('user', function ($q) {
                $q->where('active', BaseConstants::ACTIVE);
                $q->where('type', 1);
            })
            ->orderBy('updated_at', 'DESC')
            ->select('name', 'slug', 'thumbnail', 'updated_at')
            ->get();
        return response()->view('sitemap.stories', compact('stories'))
            ->header('Content-Type', 'text/xml');
    }

    public function chapters()
    {
        $chapters = Chapter::with(
            [
                'story' => function ($query) {
                    $query->select('id', 'name', 'slug', 'thumbnail', 'updated_at');
                }
            ]
        )
            ->orderBy('updated_at', 'DESC')
            ->get(['name', 'story_id', 'slug', 'updated_at']);
        return response()->view('sitemap.chapter', compact('chapters'))
            ->header('Content-Type', 'text/xml');
    }

    public function authors()
    {
        $authors = User::where('name', '<>', '')
            ->where('type', User::UserType['TranslateTeam'])
            ->get(['name', 'id', 'created_at', 'avatar']);
        return response()->view('sitemap.author', compact('authors'))->header('Content-Type', 'text/xml');
    }
}
