<?php

namespace App\Http\Controllers;

use App\Constants\BaseConstants;
use App\Libraries\Helpers;
use App\Models\Bookmark;
use App\Models\CommentStory;
use App\Models\FavouriteStory;
use App\Models\Page;
use App\Models\RecommendedStory;
use App\Models\StoryReview;
use App\Models\User;
use App\Tasks\TelegramTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Story;
use App\Models\Chapter;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class MainController extends Controller
{
    private $telegramTask;
    public function __construct(){
        $this->telegramTask = new TelegramTask();
    }

    private $selectDataStory = [
        'id',
        'user_id',
        'name',
        'content',
        'slug',
        'thumbnail',
        'total_view',
        'total_bookmark',
        'last_chapter',
        'is_full',
        'rating',
        'created_at',
        'updated_at',
    ];

    public function index(Request $request)
    {
        if (isset($request->page)) {
            return redirect()->route('pageStory', ['page' => $request->page]);
        }

        $expiresAt = Carbon::now()->addMinutes(5);
        $homepageContent = Cache::remember('homepageContent' , $expiresAt, function () use ($request, $expiresAt) {
            $data = Cache::remember('pageStory_page_1', Carbon::now()->addMinutes(5), function () {
                $list = Story::with(['user' => function ($query) {
                    $query->select('id', 'name');
                }])
                    ->orderBy('updated_at', 'DESC')
                    ->whereHas('user', function ($q) {
                        $q->where('active', BaseConstants::ACTIVE);
                        $q->where('type', 1);
                    })
                    ->where('status', BaseConstants::ACTIVE)
                    ->where('last_chapter', '<>', '')
                    ->paginate(24, $this->selectDataStory);

                $paginate = $list->links('vendor.pagination.rocker-pagination')->render();
                return [
                    'list' => json_encode($list),
                    'paginate' => $paginate
                ];
            });
            $list = json_decode($data['list']);
            $paginate = $data['paginate'];

            $completedStories = json_decode(
                Cache::remember('completed_stories_home' , $expiresAt, function () {
                    return json_encode(
                        Story::orderBy('updated_at', 'DESC')
                            ->where('is_full', BaseConstants::ACTIVE)
                            ->where('status', BaseConstants::ACTIVE)
                            ->whereHas('user', function ($q) {
                                $q->where('active', BaseConstants::ACTIVE);
                                $q->where('type', 1);
                            })
                            ->take(20)
                            ->get($this->selectDataStory)
                    );
                })
            );

            $today = Carbon::today();
            $recommendedStories = RecommendedStory::where('date', $today->toDateString())
                ->first();
            if ($recommendedStories) {
                $group_data = json_decode($recommendedStories->group_data, true);
                $stories = [];

                foreach ($group_data as $item) {
                    $story = Story::with(['user'])->where('id', $item['story_id'])->first();
                    if ($story) {
                        $stories[] = $story;
                    }
                }
                $recommendedStories->stories = $stories;
            }

            $creativeStories = Story::where('creative', BaseConstants::ACTIVE)
                ->whereHas('user', function ($q) {
                    $q->where('active', BaseConstants::ACTIVE);
                    $q->where('type', 1);
                })
                ->where('status', BaseConstants::ACTIVE)
                ->orderBy('updated_at', 'DESC')
                ->take(15)
                ->get($this->selectDataStory);

            return view('block.home', compact('list', 'paginate', 'completedStories', 'recommendedStories', 'creativeStories'))->render();
        });

        return view('home', compact('homepageContent'));
    }

    public function pageStory(Request $request)
    {
        $cacheKey = 'pageStory_page_' . md5(json_encode($request->all()));
        $data = Cache::remember($cacheKey, Carbon::now()->addMinutes(5), function () {
            $list = Story::with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
                ->orderBy('updated_at', 'DESC')
                ->whereHas('user', function ($q) {
                    $q->where('active', BaseConstants::ACTIVE);
                    $q->where('type', 1);
                })
                ->where('status', BaseConstants::ACTIVE)
                ->where('last_chapter', '<>', '')
                ->paginate(24, $this->selectDataStory);

            $paginate = $list->links('vendor.pagination.rocker-pagination')->render();
            return [
                'list' => json_encode($list),
                'paginate' => $paginate
            ];
        });
        $list = json_decode($data['list']);
        $paginate = $data['paginate'];
        return view('story.index', compact('list', 'paginate'));
    }

    public function completedStory(Request $request)
    {
        $cacheKey = 'completedStory_paginate_page_' . md5(json_encode($request->all()));
        $data = Cache::remember($cacheKey, Carbon::now()->addMinutes(5), function () use ($request) {
            $orderBy = 'updated_at DESC';
            if ($request->sort != "") {
                switch ($request->sort) {
                    case 'view_desc':
                        $orderBy = 'total_view DESC';
                        break;
                    case 'view_asc':
                        $orderBy = 'total_view ASC';
                        break;
                    case 'chapter_desc':
                        $orderBy = 'total_chapter DESC';
                        break;
                    case 'chapter_asc':
                        $orderBy = 'total_chapter ASC';
                        break;
                    default:
                        $orderBy = 'updated_at DESC';
                        break;
                }
            }

            $list = Story::with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
                ->where('is_full', BaseConstants::ACTIVE)
                ->whereHas('user', function ($q) {
                    $q->where('active', BaseConstants::ACTIVE);
                    $q->where('type', 1);
                })
                ->where('status', BaseConstants::ACTIVE)
                ->orderByRaw($orderBy)
                ->paginate(24, $this->selectDataStory);

            $paginate = $list->links('vendor.pagination.rocker-pagination')->render();
            return [
                'list' => json_encode($list),
                'paginate' => $paginate
            ];
        });
        $list = json_decode($data['list']);
        $paginate = $data['paginate'];
        return view('story.full', compact('list', 'paginate'));
    }

    public function hotStory(Request $request)
    {
        $cacheKey = 'hotStory_paginate_page_' . md5(json_encode($request->all()));
        $data = Cache::remember($cacheKey, Carbon::now()->addMinutes(5), function () use ($request) {
            $orderBy = 'total_view DESC';
            $list = Story::with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
                ->where('is_full', BaseConstants::ACTIVE)
                ->whereHas('user', function ($q) {
                    $q->where('active', BaseConstants::ACTIVE);
                    $q->where('type', 1);
                })
                ->where('status', BaseConstants::ACTIVE)
                ->orderByRaw($orderBy)
                ->paginate(24, $this->selectDataStory);

            $paginate = $list->links('vendor.pagination.rocker-pagination')->render();
            return [
                'list' => json_encode($list),
                'paginate' => $paginate
            ];
        });
        $list = json_decode($data['list']);
        $paginate = $data['paginate'];
        return view('story.hot', compact('list', 'paginate'));
    }

    public function creativeStory(Request $request)
    {
        $cacheKey = 'creativeStory_page_' . md5(json_encode($request->all()));
        $data = Cache::remember($cacheKey, Carbon::now()->addMinutes(5), function () use ($request) {
            $orderBy = 'updated_at DESC';
            if ($request->sort != "") {
                switch ($request->sort) {
                    case 'view_desc':
                        $orderBy = 'total_view DESC';
                        break;
                    case 'view_asc':
                        $orderBy = 'total_view ASC';
                        break;
                    case 'chapter_desc':
                        $orderBy = 'total_chapter DESC';
                        break;
                    case 'chapter_asc':
                        $orderBy = 'total_chapter ASC';
                        break;
                    default:
                        $orderBy = 'updated_at DESC';
                        break;
                }
            }

            $list = Story::with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
                ->where('creative', BaseConstants::ACTIVE)
                ->whereHas('user', function ($q) {
                    $q->where('active', BaseConstants::ACTIVE);
                    $q->where('type', 1);
                })
                ->where('status', BaseConstants::ACTIVE)
                ->orderByRaw($orderBy)
                ->paginate(24, $this->selectDataStory);

            $paginate = $list->links('vendor.pagination.rocker-pagination')->render();
            return [
                'list' => json_encode($list),
                'paginate' => $paginate
            ];
        });
        $list = json_decode($data['list']);
        $paginate = $data['paginate'];

        return view('story.creative', compact('list', 'paginate'));
    }

    public function longStory(Request $request)
    {
        $cacheKey = 'longStory_page_' . md5(json_encode($request->all()));
        $data = Cache::remember($cacheKey, Carbon::now()->addMinutes(5), function () use ($request) {
            $orderBy = 'updated_at DESC';
            if ($request->sort != "") {
                switch ($request->sort) {
                    case 'view_desc':
                        $orderBy = 'total_view DESC';
                        break;
                    case 'view_asc':
                        $orderBy = 'total_view ASC';
                        break;
                    case 'chapter_desc':
                        $orderBy = 'total_chapter DESC';
                        break;
                    case 'chapter_asc':
                        $orderBy = 'total_chapter ASC';
                        break;
                    default:
                        $orderBy = 'updated_at DESC';
                        break;
                }
            }
            $list = Story::with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
                ->where('total_chapter', '>', 40)
                ->whereHas('user', function ($q) {
                    $q->where('active', BaseConstants::ACTIVE);
                    $q->where('type', 1);
                })
                ->where('status', BaseConstants::ACTIVE)
                ->orderByRaw($orderBy)
                ->paginate(24, $this->selectDataStory);

            $paginate = $list->links('vendor.pagination.rocker-pagination')->render();
            return [
                'list' => json_encode($list),
                'paginate' => $paginate
            ];
        });
        $list = json_decode($data['list']);
        $paginate = $data['paginate'];
        return view('story.long-story', compact('list', 'paginate'));
    }

    public function search(Request $request)
    {
        $search = strip_tags($request->search);
        $total_chapter = strip_tags($request->total_chapter);
        $story_status = strip_tags($request->story_status);
        $cate = strip_tags($request->cate);
        if (!$search) {
            $search = "";
        }

        if (!is_string($search)) {
            return redirect()->route('index');
        }

        $query = Story::with(['user' => function ($query) {
            $query->select('id', 'name');
        }])
            ->where('status', BaseConstants::ACTIVE)
            ->where(function ($q) use ($search) {
                $q->whereHas('user', function ($query) {
                    $query->where('active', BaseConstants::ACTIVE);
                    $query->where('type', 1);
                });
                $q->where('status', BaseConstants::ACTIVE);
            });

        $orderBy = 'updated_at DESC';
        if ($request->sort != "") {
            switch ($request->sort) {
                case 'view_desc':
                    $orderBy = 'total_view DESC';
                    break;
                case 'view_asc':
                    $orderBy = 'total_view ASC';
                    break;
                case 'chapter_desc':
                    $orderBy = 'total_chapter DESC';
                    break;
                case 'chapter_asc':
                    $orderBy = 'total_chapter ASC';
                    break;
                default:
                    $orderBy = 'updated_at DESC';
                    break;
            }
        }
        $query->orderByRaw($orderBy);

        if ($search != '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
                $q->orWhere('slug', 'like', '%' . Str::slug($search) . '%');
                $q->orWhere('author', 'like', '%' . $search . '%');
                $q->orWhereHas('user', function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                });
            });
        }

        if ($total_chapter != '') {
            if ($total_chapter == '<10') {
                $query->where('total_chapter', '<=', 10);
            } elseif ($total_chapter == '11-50') {
                $query->where(function ($q) {
                    $q->where('total_chapter', '>', 10);
                    $q->where('total_chapter', '<=', 50);
                });
            } elseif ($total_chapter == '50-100') {
                $query->where(function ($q) {
                    $q->where('total_chapter', '>', 50);
                    $q->where('total_chapter', '<=', 100);
                });
            } elseif ($total_chapter == '100+') {
                $query->where('total_chapter', '>', 100);
            }
        }

        if ($story_status != '') {
            if ($story_status == 'full') {
                $query->where('is_full', BaseConstants::ACTIVE);
            } else {
                $query->where('is_full', BaseConstants::INACTIVE);
            }
        }

        if ($cate != '') {
            $cateArr = explode(',', $cate);
            if (count($cateArr)) {
                $query->where(function ($q) use ($cateArr) {
                    $q->whereJsonContains('categories', $cateArr);
                });
            }
        }

        $list = $query->paginate(24, $this->selectDataStory);

        $total_stories = $query->count();
        return view('story.search', compact('list', 'total_stories'));
    }

    public function pageHistories()
    {
        $list_categories = Helpers::getListCategories();
        return view('page.history', compact('list_categories'));
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)
            ->first();
        if ($page) {
            return view('page.index', compact('page', 'slug'));
        } else {
            abort(404);
        }
    }

    public function category(Request $request, $slug)
    {
        $story_category = json_decode(
            Cache::remember('category_' . $slug, Carbon::now()->addMinutes(5), function () use ($slug) {
                return json_encode(
                    Category::where('slug', $slug)
                        ->where('status', BaseConstants::ACTIVE)
                        ->first(
                            [
                                'id',
                                'name',
                                'slug',
                            ]
                        )
                );
            })
        );

        if ($story_category) {
            $orderBy = 'updated_at DESC';
            if ($request->sort != "") {
                switch ($request->sort) {
                    case 'view_desc':
                        $orderBy = 'total_view DESC';
                        break;
                    case 'view_asc':
                        $orderBy = 'total_view ASC';
                        break;
                    case 'chapter_desc':
                        $orderBy = 'total_chapter DESC';
                        break;
                    case 'chapter_asc':
                        $orderBy = 'total_chapter ASC';
                        break;
                    default:
                        $orderBy = 'updated_at DESC';
                        break;
                }
            }

            $cacheKey = 'story_category_' . $slug . '_' . md5(json_encode($request->all()));
            $data = Cache::remember($cacheKey, Carbon::now()->addMinutes(5), function () use ($story_category, $orderBy) {
                $list = Story::with(['user' => function ($query) {
                    $query->select('id', 'name');
                }])
                    ->whereJsonContains('categories', (string)$story_category->id)
                    ->whereHas('user', function ($q) {
                        $q->where('active', BaseConstants::ACTIVE);
                        $q->where('type', 1);
                    })
                    ->where('status', BaseConstants::ACTIVE)
                    ->orderByRaw($orderBy)
                    ->paginate(24, [
                        'user_id',
                        'name',
                        'content',
                        'slug',
                        'thumbnail',
                        'total_view',
                        'total_bookmark',
                        'last_chapter',
                        'is_full',
                        'rating',
                        'updated_at',
                    ]);
                $paginate = $list->links('vendor.pagination.rocker-pagination')->render();
                return [
                    'list' => json_encode($list),
                    'paginate' => $paginate
                ];
            });

            $list = json_decode($data['list']);
            $paginate = $data['paginate'];
            return view('story.category', compact('story_category', 'list', 'paginate'));
        } else {
            abort(404);
        }
    }

    public function storyDetail($slug)
    {
        $expiresAt5 = Carbon::now()->addMinutes(5);
        $expiresAt10 = Carbon::now()->addMinutes(10);

        $story = json_decode(
            Cache::remember('story_' . $slug, $expiresAt5, function () use ($slug) {
                return json_encode(
                    Story::where('slug', $slug)
                        ->whereHas('user', function ($q) {
                            $q->where('active', BaseConstants::ACTIVE);
                            $q->where('type', 1);
                        })
                        ->where('status', BaseConstants::ACTIVE)
                        ->first(
                            [
                                'id',
                                'user_id',
                                'total_chapter',
                                'name',
                                'slug',
                                'another_name',
                                'author',
                                'categories',
                                'thumbnail',
                                'content',
                                'total_view',
                                'total_listen',
                                'total_review',
                                'total_bookmark',
                                'total_favourite',
                                'rating',
                                'is_full',
                                'audio',
                                'warning',
                                'creative',
                                'created_at',
                                'updated_at'
                            ]
                        )
                );
            })
        );

        if ($story) {
            $list_chapters = json_decode(
                Cache::remember('list_chapters_story_id_' . $story->id, Carbon::now()->addMinutes(3), function () use ($story) {
                    return json_encode(
                        Chapter::where('story_id', $story->id)
                            ->where('status', BaseConstants::ACTIVE)
                            ->orderBy('created_at', 'DESC')
                            ->select(
                                'id',
                                'name',
                                'slug',
                                'view',
                                'coin',
                                'created_at'
                            )
                            ->get()
                    );
                })
            );

            $categories = json_decode(
                Cache::remember('list_categories_story_id_' . $story->id, $expiresAt5, function () use ($story) {
                    $categories = json_decode($story->categories);
                    return json_encode(Category::whereIn('id', $categories)->get(['id', 'name', 'slug']));
                })
            );

            $author = Helpers::getAuthorById($story->user_id);

            $is_bookmark = false;
            $is_favourite = false;
            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $bookmark = json_decode(
                    Cache::remember('bookmark_of_user_' . $user_id, $expiresAt10, function () use ($user_id) {
                        return json_encode(Bookmark::where('user_id', $user_id)->pluck('story_id')->toArray());
                    })
                );
                if (in_array($story->id, $bookmark)) {
                    $is_bookmark = true;
                }

                $is_favourite = Cache::remember('favourite_' . $user_id . '_' . $story->id, $expiresAt10, function () use ($user_id, $story) {
                    $favouriteCheck = FavouriteStory::where('user_id', $user_id)
                        ->where('story_id', $story->id)
                        ->first();
                    if ($favouriteCheck) {
                        return true;
                    }
                    return false;
                });
            }

            $total_cr = Cache::remember('total_comments_reviews_story_id_' . $story->id, $expiresAt5, function () use ($story) {
                $total_comments = CommentStory::where('story_id', $story->id)->count();
                $total_reviews = StoryReview::where('story_id', $story->id)
                    ->where('status', StoryReview::Status['Duyệt'])
                    ->where('content', '<>', '')
                    ->count();
                return [
                    'comments' => $total_comments,
                    'reviews' => $total_reviews
                ];
            });
            $total_comments = $total_cr['comments'];
            $total_reviews = $total_cr['reviews'];

            return view(
                'story.detail',
                compact(
                    'story',
                    'list_chapters',
                    'author',
                    'categories',
                    'is_bookmark',
                    'total_comments',
                    'total_reviews',
                    'is_favourite'
                )
            );
        } else {
            abort(404);
        }
    }

    public function singleDetails($slug1, $slug2, Request $request)
    {
        $expiresAt5 = Carbon::now()->addMinutes(5);
        $expiresAt10 = Carbon::now()->addMinutes(10);

        $chapter = Cache::remember($slug1 . '_chapter_' . $slug2, $expiresAt5, function () use ($slug1, $slug2) {
            $chapter = Chapter::with(
                [
                    'story' => function ($query) {
                        $query->select('id', 'name', 'slug', 'thumbnail', 'user_id', 'type', 'warning');
                    }
                ]
            )
                ->whereHas('story.user', function ($q) {
                    $q->where('active', BaseConstants::ACTIVE);
                    $q->where('type', 1);
                })
                ->whereHas('story', function ($query) use ($slug1) {
                    $query->where('slug', $slug1);
                    $query->where('status', BaseConstants::ACTIVE);
                })
                ->where('slug', $slug2)
                ->where('status', BaseConstants::ACTIVE)
                ->first();
            if ($chapter) {
                try {
                    $classNames = Helpers::spanClassNameArray();

                    $author = Helpers::getAuthorById($chapter->user_id);

                    $contentHtml = Helpers::replaceWithSpans($chapter->content, $classNames);
                    $contentHtml = htmlspecialchars_decode($contentHtml);
                    $contentHtml = str_replace('<br>', '</p><p>', $contentHtml);
                    $contentHtml = str_replace('<br/>', '</p><p>', $contentHtml);
                    if ($contentHtml != '') {
                        $chapterUrl = route('chapter.detail', [$slug1, $slug2]);
                        $addText = '<p class="signature">[Truyện được đăng tải duy nhất tại dammy.me - <a href="' . $chapterUrl . '">' . $chapterUrl . '</a>.]</p>';
                        $dom = new \DOMDocument();
                        libxml_use_internal_errors(true); // Bỏ qua lỗi HTML không hợp lệ
                        $dom->loadHTML(mb_convert_encoding($contentHtml, 'HTML-ENTITIES', 'UTF-8'));
                        libxml_clear_errors();

                        // Lấy tất cả các thẻ <p>
                        $paragraphs = $dom->getElementsByTagName('p');
                        $countP = $paragraphs->length;

                        $newParagraph = $dom->createDocumentFragment();
                        $newParagraph->appendXML($addText);

                        // Chèn đoạn text vào vị trí giữa
                        $middleIndex = floor($countP / 2);
                        $parent = $paragraphs->item($middleIndex)->parentNode;
                        $referenceNode = $paragraphs->item($middleIndex);
                        $parent->insertBefore($newParagraph, $referenceNode);

                        if (isset($author->team_signature) && $author->team_signature != '') {
                            $insertText = '<p class="signature">' . nl2br($author->team_signature) . '</p>';
                            $randomNum = rand(2, $countP);

                            $signatureParagraph = $dom->createDocumentFragment();
                            $signatureParagraph->appendXML($insertText);

                            $parent = $paragraphs->item($randomNum)->parentNode;
                            $referenceNode = $paragraphs->item($randomNum);
                            $parent->insertBefore($signatureParagraph, $referenceNode);
                        }

                        $contentHtml = $dom->saveHTML();
                        $contentHtml = str_replace('<body>', '', $contentHtml);
                        $contentHtml = str_replace('</body>', '', $contentHtml);
                        $contentHtml = str_replace('<html>', '', $contentHtml);
                        $contentHtml = str_replace('</html>', '', $contentHtml);
                    }
                } catch (\Exception $e) {
                    $contentHtml = htmlspecialchars_decode($chapter->content);
                }
                $chapter->content = $contentHtml;
            }
            return json_encode($chapter);
        });

        $chapter = json_decode($chapter);
        if ($chapter) {
            $author = Helpers::getAuthorById($chapter->user_id);
            $userIP = Helpers::getUserIP();
            Cache::forget('check_user_ip_' . $userIP);
            Cache::put(
                'check_user_ip_' . $userIP,
                json_encode([
                    'ip' => $userIP,
                    'chapter_id' => $chapter->id,
                    'time' => date("Y-m-d H:i:s")
                ]),
                $expiresAt5
            );

            $list_chapters = json_decode(
                Cache::remember('list_chapters_story_id_' . $chapter->story->id, Carbon::now()->addMinutes(3), function () use ($chapter) {
                    return json_encode(
                        Chapter::where('story_id', $chapter->story->id)
                            ->where('status', BaseConstants::ACTIVE)
                            ->orderBy('created_at', 'DESC')
                            ->select(
                                'id',
                                'name',
                                'slug',
                                'view',
                                'coin',
                                'created_at'
                            )
                            ->get()
                    );
                })
            );

            $is_favourite = false;
            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $is_favourite = Cache::remember('favourite_' . $user_id . '_' . $chapter->story->id, $expiresAt10, function () use ($user_id, $chapter) {
                    $favouriteCheck = FavouriteStory::where('user_id', $user_id)
                        ->where('story_id', $chapter->story->id)
                        ->first();
                    if ($favouriteCheck) {
                        return true;
                    }
                    return false;
                });
            }

            //process get chapter next, prev
            $next = 0;
            $prev = 0;
            if ($list_chapters && count($list_chapters) > 0) {
                foreach ($list_chapters as $key => $item) {
                    if ($item->id == $chapter->id) {
                        $next = $key - 1;
                        $prev = $key + 1;
                        break;
                    }
                }
            }

            $next_chapter = '';
            if ($next >= 0 && isset($list_chapters[$next])) {
                $next_chapter = [
                    'storySlug' => $chapter->story->slug,
                    'chapterSlug' => $list_chapters[$next]->slug
                ];
            }
            $prev_chapter = '';
            if ($prev >= 0 && isset($list_chapters[$prev])) {
                $prev_chapter = [
                    'storySlug' => $chapter->story->slug,
                    'chapterSlug' => $list_chapters[$prev]->slug
                ];
            }

            return view('story.chapter', compact('chapter', 'list_chapters', 'next_chapter', 'prev_chapter', 'author', 'is_favourite'));
        } else {
            $story = Story::where('slug', $slug1)
                ->where('status', BaseConstants::ACTIVE)
                ->first();
            if ($story) {
                return redirect()
                    ->route('story.detail', $story->slug)
                    ->withErrors('Không tìm thấy chương truyện mà bạn cần đọc. Vui lòng xem lại danh sách chương bên dưới truyện.');
            } else {
                abort(404);
            }
        }
    }

    public function NotFound()
    {
        abort(404);
    }

    public function PageLienHe()
    {
        return view('page.lien_he');
    }

    public function storeContact(Request $request)
    {
        $this->validate(
            $request,
            [
                'your_name' => 'required',
                'your_email' => 'required|email',
                'your_message' => 'required',
            ],
            [
                'your_name.required' => 'Mời Bạn nhập vào tên của bạn',
                'your_email.required' => 'Mời bạn nhập Email',
                'your_message.required' => 'Mời bạn nhập nội dung',
            ]
        );
        $email_admin = Helpers::get_setting('admin_email');
        $cc_email = Helpers::get_setting('cc_mail');
        $name_admin_email = env('MAIL_FROM_NAME');
        $subject_default = Helpers::get_option('title-email');
        $data = [
            'your_name' => $request->your_name,
            'your_email' => $request->your_email,
            'your_message' => $request->your_message,
            'email_admin' => $email_admin,
            'cc_email' => $cc_email,
            'name_email_admin' => $name_admin_email,
            'subject_default' => $subject_default
        ];
        $note_message_contact = Helpers::get_option('note-email');
        Mail::send(
            'email.contact',
            $data,
            function ($message) use ($data) {
                $message->from($data['your_email'], $data['name_email_admin']);
                $message->to($data['email_admin'])
                    ->cc($data['cc_email'], $data['name_email_admin'])
                    ->subject($data['subject_default'] . "- Người dùng:" . $data['your_name']);
            }
        );
        return redirect()->route('lien-he')
            ->with('success_msg', $note_message_contact);
    }

    public function reportLicense(Request $request)
    {
        if (env('APP_ENV') == 'production') {
            $violate_link = strip_tags($request->violate_link);
            $original_link = strip_tags($request->original_link);
            $email = strip_tags($request->report_email);
            $userIP = Helpers::getUserIP();
            if (strpos($email, 'test') !== false) {
                Log::debug($userIP);
                return response()->json(['success' => false, 'message' => 'Bye']);
            }

            $text = "<b>[" . Helpers::get_setting('company_name') . "]</b> Báo cáo vi phạm bản quyền\n"
                . "<b>Email: </b> $email \n"
                . "<b>IP: </b> $userIP \n"
                . "<b>Link vi phạm: </b> $violate_link \n"
                . "<b>Link tác phẩm gốc: </b> $original_link \n";
            $this->telegramTask->sendMessage($text);
        }

        $msg = 'Cảm ơn bạn đã gửi báo cáo vi phạm bản quyền đến Đam Mỹ. Team sẽ xem xét và liên hệ bạn trong thời gian sớm nhất!';
        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function getLinkPR(Request $request)
    {
        if ($request->pr == 1) {
            $id = $request->id;
            $story = null;
            $firstChapter = null;
            if ($id) {
                $story = Story::where('id', $id)->first(['id', 'name', 'slug']);
                $firstChapter = Chapter::where('story_id', $story->id)
                    ->orderBy('created_at', 'ASC')
                    ->first(['slug']);
            }
            return view('page.link-pr', compact('story', 'firstChapter'));
        }
        abort(404);
    }
}
