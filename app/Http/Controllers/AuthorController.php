<?php

namespace App\Http\Controllers;

use App\Constants\BaseConstants;
use App\Libraries\Helpers;
use App\Models\Follower;
use App\Models\Story;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthorController extends Controller
{
    public function index(Request $request) {
        if ($request->search != '') {
            $query = User::where('type', User::UserType['TranslateTeam'])
                ->where('active', BaseConstants::ACTIVE)
                ->where('name', 'like', '%' . $request->search . '%');
            $translateTeams = $query->orderBy('total_view', 'DESC')
                ->paginate(24, ['id', 'name', 'avatar', 'total_view']);
            foreach ($translateTeams as $team) {
                $team->total_stories = Story::where('user_id', $team->id)
                    ->count();
            }
            $paginate = $translateTeams->withQueryString()->links('vendor.pagination.rocker-pagination')->render();
        } else {
            $page = ($request->page) ? $request->page : 1;
            $data = Cache::remember('translateTeam.index.' . $page, Carbon::now()->addMinutes(5), function () {
                $translateTeams = User::where('type', User::UserType['TranslateTeam'])
                    ->where('active', BaseConstants::ACTIVE)
                    ->orderBy('total_view', 'DESC')
                    ->paginate(24, ['id', 'name', 'avatar', 'total_view']);
                foreach ($translateTeams as $team) {
                    $team->total_stories = Story::where('user_id', $team->id)
                        ->count();
                }
                $paginate = $translateTeams->links('vendor.pagination.rocker-pagination')->render();
                return [
                    'list' => json_encode($translateTeams),
                    'paginate' => $paginate
                ];
            });
            $translateTeams = json_decode($data['list']);
            $paginate = $data['paginate'];
        }

        return view('author.index', compact('translateTeams', 'paginate'));
    }

    public function detail(Request $request, $id)
    {
        $author = Helpers::getAuthorById($id);
        $expiresAt10 = Carbon::now()->addMinutes(10);

        if ($author) {
            $is_follow = false;
            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $follower_ids = json_decode(
                    Cache::remember('follow_of_user_' . $user_id, $expiresAt10, function () use ($user_id) {
                        return json_encode(Follower::where('user_id', $user_id)->pluck('follow_user_id')->toArray());
                    })
                );
                if (in_array($author->id, $follower_ids)) {
                    $is_follow = true;
                }
            }

            $recommendedStory = json_decode(
                Cache::remember('recommendedMyStory_' . $author->id, $expiresAt10, function () use ($author) {
                    $recommended_my_stories = $author->recommended_my_stories;
                    $myStories = ($recommended_my_stories != '') ? json_decode($recommended_my_stories, true) : [];
                    if ($myStories == null) {
                        $myStories = [];
                    }
                    $recommendedStory = Story::where('user_id', $author->id)
                        ->where('status', BaseConstants::ACTIVE)
                        ->whereIn('id', $myStories)
                        ->orderBy('total_view', 'DESC')
                        ->get(
                            [
                                'id',
                                'name',
                                'slug',
                                'thumbnail',
                                'total_view',
                                'total_bookmark',
                                'rating',
                            ]
                        );
                    return json_encode($recommendedStory);
                })
            );

            $topStoryMonth = json_decode(
                Cache::remember('topStoryMonth_' . $author->id, $expiresAt10, function () use ($author) {
                    return json_encode(
                        Story::join('story_views', 'story_views.story_id', 'stories.id')
                            ->where('stories.user_id', $author->id)
                            ->where('story_views.month', '>', 0)
                            ->orderBy('story_views.month', 'DESC')
                            ->take(15)
                            ->select(
                                'stories.id',
                                'stories.name',
                                'stories.slug',
                                'stories.thumbnail',
                                'stories.total_view',
                                'stories.total_bookmark',
                                'stories.rating',
                            )
                            ->get()
                    );
                })
            );

            $orderBy = 'created_at DESC';
            if ($request->order != "") {
                switch ($request->order) {
                    case 'latest':
                        $orderBy = 'updated_at DESC';
                        break;
                    default:
                        $orderBy = 'total_view DESC';
                        break;
                }
            }

            $selectedField = [
                'id',
                'user_id',
                'name',
                'slug',
                'thumbnail',
                'total_view',
                'total_bookmark',
                'last_chapter',
                'is_full',
                'rating',
                'updated_at',
            ];
            if (Auth::check() && Auth::user()->id == $author->id && $request->search != '') {
                $query = Story::where('user_id', $author->id)
                    ->whereHas('user', function ($q) {
                        $q->where('active', BaseConstants::ACTIVE);
                        $q->where('type', 1);
                    })
                    ->where('status', BaseConstants::ACTIVE)
                    ->orderBy('created_at', 'DESC');

                $search = $request->search;
                if ($search != '') {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%');
                        $q->orWhere('slug', 'like', '%' . Str::slug($search) . '%');
                    });
                }

                $list = $query->paginate(24, $selectedField);
                $paginate = $list->links('vendor.pagination.rocker-pagination')->render();
            } else {
                $page = ($request->page) ? $request->page : 1;
                $data = Cache::remember(
                    'authorDetail_' . $id . '_page_' . $page,
                    Carbon::now()->addMinutes(5), function () use ($author, $orderBy, $selectedField) {
                    $list = Story::where('user_id', $author->id)
                        ->where('status', BaseConstants::ACTIVE)
                        ->whereHas('user', function ($q) {
                            $q->where('active', BaseConstants::ACTIVE);
                            $q->where('type', 1);
                        })
                        ->orderByRaw($orderBy)
                        ->paginate(24, $selectedField);
                    $paginate = $list->links('vendor.pagination.rocker-pagination')->render();
                    return [
                        'list' => json_encode($list),
                        'paginate' => $paginate
                    ];
                }
                );

                $list = json_decode($data['list']);
                $paginate = $data['paginate'];
            }

            return view('author.single', compact('author', 'list', 'paginate', 'is_follow', 'recommendedStory', 'topStoryMonth'));
        } else {
            abort(404);
        }
    }
}
