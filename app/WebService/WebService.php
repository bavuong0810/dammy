<?php

namespace App\WebService;

use App\Constants\BaseConstants;
use App\Models\Category;
use App\Models\Chat;
use App\Models\Story;
use App\Libraries\Helpers;
use App\Models\StoryReview;
use App\Models\StoryReviewComment;
use App\Models\StoryView;
use App\Models\CommentStory;
use App\Models\Page;
use App\Models\User;
use App\Models\UserCoin;
use App\Models\UserView;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class WebService
{
    // SEO
    public function getSEO($data = array())
    {
        $seo = array();
        $seo['title'] = isset($data['title']) ? $data['title'] : '';
        $seo['keywords'] = isset($data['keywords']) ? $data['keywords'] : '';
        $seo['description'] = isset($data['description']) ? $data['description'] : '';
        $seo['og_title'] = isset($data['og_title']) ? $data['og_title'] : '';
        $seo['og_description'] = isset($data['og_description']) ? $data['og_description'] : '';
        $seo['og_url'] = isset($data['og_url']) ? $data['og_url'] : '';
        $seo['og_img'] = isset($data['og_img']) ? $data['og_img'] : '';
        $seo['current_url'] = isset($data['current_url']) ? $data['current_url'] : '';
        $seo['current_url_amp'] = isset($data['current_url_amp']) ? $data['current_url_amp'] : '';
        return $seo;
    }//end function

    public static function convertPermission($permission)
    {
        $permissions = [
            'create' => 1,
            'read' => 2,
            'update' => 4,
            'delete' => 8,
        ];

        $arr_permission = array();
        foreach ($permissions as $permission_key => $value) {
            if ($value & $permission) {
                array_push($arr_permission, $permission_key);
            }
        }
        return $arr_permission;
    }

    public static function ReviewRender($id, $pageNumber = 1)
    {
        Carbon::setLocale('vi');
        $expiresAt = Carbon::now()->addMinutes(5);
        if (Cache::has('reviews_story_' . $pageNumber . '_id_' . $id)) {
            $reviews = Cache::get('reviews_story_' . $pageNumber . '_id_' . $id);
            $reviews = json_decode($reviews);
        } else {
            $reviews = StoryReview::with(['user'])
                ->where('story_id', $id)
                ->where('content', '<>', '')
                ->where('status', BaseConstants::ACTIVE)
                ->orderBy('created_at', 'DESC')
                ->paginate(15, ['*'], 'page', $pageNumber);
            $reviews = json_encode($reviews);
            Cache::put('reviews_story_' . $pageNumber . '_id_' . $id, $reviews, $expiresAt);
            $reviews = json_decode($reviews);
        }
        return view('partials.block-review', compact('reviews'))->render();
    }

    public static function ReviewCommentRender($id)
    {
        $expiresAt = Carbon::now()->addMinutes(5);
        if (Cache::has('reviews_comments_id_' . $id)) {
            $comments = Cache::get('reviews_comments_id_' . $id);
            $comments = json_decode($comments);
        } else {
            $comments = StoryReviewComment::with(['user'])
                ->where('review_id', $id)
                ->orderBy('created_at', 'ASC')
                ->get();
            Cache::put('reviews_comments_id_' . $id, $comments, $expiresAt);
        }
        $review_id = $id;
        return view('partials.block-review-comment', compact('comments', 'review_id'))->render();
    }

    public static function CommentStory($id)
    {
        Carbon::setLocale('vi');
        $expiresAt5 = Carbon::now()->addMinutes(5);

        $html = '';
        $data_comment = json_decode(
            Cache::remember('comment_story_id_' . $id, $expiresAt5, function () use ($id) {
                $data_comment = CommentStory::with(
                    [
                        'user' => function ($q) {
                            $q->select('id', 'name', 'avatar');
                        }
                    ]
                )
                    ->where('story_id', $id)
                    ->where('parent', 0)
                    ->orderBy('created_at', 'DESC')
                    ->offset(0)
                    ->limit(10)
                    ->get(
                        [
                            'id',
                            'user_id',
                            'story_id',
                            'content',
                            'created_at',
                        ]
                    );

                foreach ($data_comment as $row) {
                    $row->comment_reply = CommentStory::with(
                        [
                            'user' => function ($q) {
                                $q->select('id', 'name', 'avatar');
                            }
                        ]
                    )
                        ->where('story_id', $id)
                        ->where('parent', $row->id)
                        ->orderBy('created_at', 'ASC')
                        ->get(
                            [
                                'id',
                                'user_id',
                                'story_id',
                                'content',
                                'created_at',
                            ]
                        );
                }
                return json_encode($data_comment);
            })
        );

        $total_cr = Cache::remember('total_comments_reviews_story_id_' . $id, $expiresAt5, function () use ($id) {
            $total_comments = CommentStory::where('story_id', $id)->count();
//                $total_reviews = StoryReview::where('story_id', $story->id)
//                    ->where('status', StoryReview::Status['Duyệt'])
//                    ->where('content', '<>', '')
//                    ->count();
            return [
                'comments' => $total_comments,
                'reviews' => false, //$total_reviews
            ];
        });
        $total_comment = $total_cr['comments'];

        $html .= '<div class="clear comment-website"><div class="cm-based">
          <p class="comment-count">' . $total_comment . ' bình luận</p>
          <div id="comment_loading" style="display:none; text-align:center;">
            <img alt="Please Wait" src="' . asset('img/loading.gif') . '">
          </div>
          <form name="frmContact" id="frmContact" onsubmit="comment(' . $id . ',\'\',10); return false" method="POST">
            ' . csrf_field() . '
            <div class="form row">
              <div class="form-group col-md-10 comment-text">
                <textarea name="txtContent" id="txtContent" class="form-control"
                    placeholder="Vui lòng bình luận bằng tiếng việt có dấu. Spam, chửi bậy, đưa link web khác sẽ bị ban nick"></textarea>
              </div>
              <div class="form-group col-md-2">
                <button type="submit" class="btn btn-read btn-lg btn-block" name="btnComment" id="btnComment">
                  <i class="bx bx-check"></i>
                </button>
              </div>
              <div class="clearfix"></div>
            </div>
          </form>
          <div id="comment-done" class="blog-comment">
            <ul class="comments">';
        if ($data_comment && count($data_comment) > 0) {
            foreach ($data_comment as $row) {
                $created = date('Y-m-d H:i:s', strtotime($row->created_at));
                $row->created = Carbon::parse($created)->diffForHumans(Carbon::now());

                $users_comment = $row->user;
                if ($users_comment) {
                    if ($users_comment->avatar != "") {
                        $thumbnail = asset('images/avatar/thumbs/100/' . $users_comment->avatar);
                    } else {
                        $thumbnail = asset('img/avata.png');
                    }
                    $avt = '<img src="' . $thumbnail . '" alt="">';

                    $html .= '<li class="comment_' . $row->id . '">
                    <div class="avt_user">
                      ' . $avt . '
                    </div>
                    <div class="post-comments">
                      <p>' . $row->content . '</p>
                      <p class="meta-2">
                        <a href="javascript:void(0)"><abbr title="Thành viên">' . $users_comment->name . '</abbr></a>
                        <small class="pull-right">' . $row->created . ' · <a href="javascript:void(0)" onclick="commentReply(' . $row->story_id . ',' . $row->id . ',\'/img/avata.png\')">Trả lời</a></small></p>
                    </div>';
                    $comment_id_parent = $row->id;
                    if ($row->comment_reply) {
                        $html .= '<ul>';
                        foreach ($row->comment_reply as $item) {
                            $created = date('Y-m-d H:i:s', strtotime($item->created_at));
                            $item->created = Carbon::parse($created)->diffForHumans(Carbon::now());

                            $users_comment_child = $item->user;
                            if ($users_comment_child) {
                                if ($users_comment_child->avatar != "") {
                                    $thumbnail = asset('images/avatar/thumbs/100/' . $users_comment_child->avatar);
                                } else {
                                    $thumbnail = asset('img/avata.png');
                                }
                                $avt_rep = '<img src="' . $thumbnail . '" alt="">';

                                $html .= '<li class="comment_' . $item->id . '">
                                <div class="avt_user">
                                  ' . $avt_rep . '
                                </div>
                                <div class="post-comments">
                                  <p>' . $item->content . '</p>
                                  <p class="meta-2">
                                    <a href="javascript:void(0)"><abbr title="Thành viên">' . $users_comment_child->name . '</abbr></a>
                                    <small class="pull-right">' . $item->created . ' · <a href="javascript:void(0)" onclick="commentReply(' . $row->story_id . ',' . $comment_id_parent . ',\'/img/avata.png\')">Trả lời</a></small></p>
                                </div></li>';
                            }
                        }
                        $html .= '</ul>';
                    }
                    $html .= '</li>';
                }
            }
        }

        if ($total_comment > 10) {
            $paging = '<div class="paging text-center"><button class="btn btn-sm btn-lg btn-success" onclick="more_comments(\'' . $id . '\',\'1\',\'2\');">Xem thêm 10 bình luận</button></div>';
        } else {
            $paging = '';
        }
        $html .= '</ul>
            ' . $paging . '
          </div>
        </div></div>';
        return $html;
    }

    public static function CommentRender($id)
    {
        return self::CommentStory($id);
    }

    public function ChatBox()
    {
        if (Cache::has('chat_box')) {
            $chats = Cache::get('chat_box');
            $chats = json_decode($chats, true);
        } else {
            $chats = Chat::with(
                [
                    'user' => function ($q) {
                        $q->select('id', 'name', 'avatar', 'type');
                    }
                ]
            )
                ->orderBy('created_at', 'DESC')
                ->limit(15)
                ->offset(0)
                ->get()
                ->toArray();

            $expiresAt = Carbon::now()->addMinutes(2);
            Cache::put('chat_box', json_encode($chats), $expiresAt);
        }

        $html = '';
        $count = count($chats);
        if ($count > 0) {
            $list_chat = '';
            for ($i = ($count - 1); $i >= 0; $i--) {
                $avatar = asset('img/avata.png');
                if ($chats[$i]['user']['avatar'] != '') {
                    $avatar = asset('images/avatar/thumbs/100/' . $chats[$i]['user']['avatar']);
                }

                $name = $chats[$i]['user']['name'];
                if ($chats[$i]['user']['type'] == User::UserType['TranslateTeam']) {
                    $name = '<a href="' . route('translateTeam.detail', $chats[$i]['user']['id']) . '" class="team_chat_name">' . $chats[$i]['user']['name'] . '</a>';
                }
                $avatar = str_replace('onload=', '', $avatar);
                $list_chat .= '<div class="li_chat" data-id="' . $chats[$i]['id'] . '" data-user-id="' . $chats[$i]['user_id'] .'">
                    <div class="avatar_chat">
                        <img src="' . $avatar . '" onerror="this.src=\'' . asset('img/avata.png') . '\';" alt="">
                    </div>
                    <div class="info_chat">
                        <span class="time">' . self::time_request($chats[$i]['created_at']) . '</span>
                        <span class="content_text">
                            <span class="name level_0">' . $name . '</span><span class="text">: ' . $chats[$i]['content'] . '</span>
                        </span>
                    </div>
                </div>';
            }
            $html .= '<div class="list-chat">' . $list_chat . '</div>';
        }

        return '<input type="hidden" name="offset" id="offset" value="15">
        <div class="content_box_chat">' . $html . '</div>
        <div class="input_chat mt-2">
            <input maxlength="250" id="chat_text" placeholder="Bạn muốn nhắn điều gì..." class="form-control">
        </div>';
    }

    public function creativeStories($stories)
    {
        $html = '';
        $sectionTitle = '<h5 class="mb-0 text-uppercase">Truyện Sáng Tác </h5> <hr>';
        $blockStory = '';
        foreach ($stories as $item) {
            $link = route('story.detail', [$item->slug]);

            $blockStory .= '<div class="single-story-block">
                <div class="single-story-wrap">
                    <div class="single-story-img">
                        <a href="' . $link . '">
                            <img
                                src="' . asset('img/ajax-loading.gif') . '" class="lazyload"
                                data-src="' . asset('images/story/thumbs/230/' . $item->thumbnail) . '"
                                alt="' . $item->name . '" width="200" height="260"
                                onerror="this.src=\'' . asset('img/no-image.png') . '\'"
                            />
                        </a>
                    </div>
                </div>
                <div class="single-story-details">
                    <h3><a href="' . $link . '">' . $item->name . '</a></h3>
                </div>
            </div>';
        }
        $html .= '<div class="top-ten-stories-area">
            <div class="slider-recommend-right background-primary">
                <div class="total-item-show position-relative">
                    ' . $sectionTitle . '
                    <a href="' . route('creativeStory') . '" class="read-more-story btn btn-outline-primary radius-30 btn-sm">Xem thêm</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="creative-stories-slider owl-carousel" id="creative-story-slider">
                            ' . $blockStory . '
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        return '<section class="creative-stories-home mt-5">
            <div class="container">' . $html . '</div>
        </section>';
    }

    public function WidgetRight($getOptions)
    {
        $colorArray = [
            "#FF1493",
            "#C71585",
            "#FF00FF",
            "#DA70D6",
            "#8A2BE2",
            "#DB7093",
            "#BA55D3",
            "#FF69B4",
            "#8B008B",
            "#E30B5D"
        ];
        $html = '<div class="widget-fanpage mb-3 text-center">
            ' . Helpers::get_option_by_key($getOptions, 'fanpage-iframe') . '
        </div>';

        $expiresAt = Carbon::now()->addMinutes(30);
        //Top Ngày
        $top_day = json_decode(
            Cache::remember('top_day' , $expiresAt, function () {
                return json_encode(
                    StoryView::with(
                        [
                            'story' => function ($q) {
                                $q->select('id', 'name', 'slug', 'last_chapter', 'thumbnail');
                            }
                        ]
                    )
                        ->whereHas('story.user', function ($q) {
                            $q->where('active', BaseConstants::ACTIVE);
                            $q->whereNotIn('id', [1945]);
                            $q->where('type', 1);
                        })
                        ->orderBy('day', 'DESC')
                        ->take(10)
                        ->get(['day', 'story_id'])
                );
            })
        );

        //Top Tuần
        $top_week = json_decode(
            Cache::remember('top_week' , $expiresAt, function () {
                return json_encode(
                    StoryView::with(
                        [
                            'story' => function ($q) {
                                $q->select('id', 'name', 'slug', 'last_chapter', 'thumbnail');
                            }
                        ]
                    )
                        ->whereHas('story.user', function ($q) {
                            $q->where('active', BaseConstants::ACTIVE);
                            $q->whereNotIn('id', [1945]);
                            $q->where('type', 1);
                        })
                        ->orderBy('week', 'DESC')
                        ->take(10)
                        ->get(['week', 'story_id'])
                );
            })
        );

        //Top Tháng
        $top_month = json_decode(
            Cache::remember('top_month' , $expiresAt, function () {
                return json_encode(
                    StoryView::with(
                        [
                            'story' => function ($q) {
                                $q->select('id', 'name', 'slug', 'last_chapter', 'thumbnail');
                            }
                        ]
                    )
                        ->whereHas('story.user', function ($q) {
                            $q->where('active', BaseConstants::ACTIVE);
                            $q->whereNotIn('id', [1945]);
                            $q->where('type', 1);
                        })
                        ->orderBy('month', 'DESC')
                        ->take(10)
                        ->get(['month', 'story_id'])
                );
            })
        );

        //Top Năm
        $top_year = json_decode(
            Cache::remember('top_year' , $expiresAt, function () {
                return json_encode(
                    StoryView::with(
                        [
                            'story' => function ($q) {
                                $q->select('id', 'name', 'slug', 'last_chapter', 'thumbnail');
                            }
                        ]
                    )
                        ->whereHas('story.user', function ($q) {
                            $q->where('active', BaseConstants::ACTIVE);
                            $q->whereNotIn('id', [1945]);
                            $q->where('type', 1);
                        })
                        ->orderBy('year', 'DESC')
                        ->take(10)
                        ->get(['year', 'story_id'])
                );
            })
        );

        //Top User View Day
        $top_user_view_day = json_decode(
            Cache::remember('top_user_view_day' , $expiresAt, function () {
                return json_encode(
                    UserView::with(['user'])
                        ->whereNotIn('user_id', [1945])
                        ->whereHas('user', function ($q) {
                            $q->where('active', BaseConstants::ACTIVE);
                            $q->where('type', 1);
                        })
                        ->orderBy('day', 'DESC')
                        ->take(10)
                        ->get(['day', 'user_id'])
                );
            })
        );

        //Top User View Week
        $top_user_view_week = json_decode(
            Cache::remember('top_user_view_week' , $expiresAt, function () {
                return json_encode(
                    UserView::with(['user'])
                        ->whereNotIn('user_id', [1945])
                        ->whereHas('user', function ($q) {
                            $q->where('active', BaseConstants::ACTIVE);
                            $q->where('type', 1);
                        })
                        ->orderBy('week', 'DESC')
                        ->take(10)
                        ->get(['week', 'user_id'])
                );
            })
        );

        //Top User View Month
        $top_user_view_month = json_decode(
            Cache::remember('top_user_view_month' , $expiresAt, function () {
                return json_encode(
                    UserView::with(['user'])
                        ->whereNotIn('user_id', [1945])
                        ->whereHas('user', function ($q) {
                            $q->where('active', BaseConstants::ACTIVE);
                            $q->where('type', 1);
                        })
                        ->orderBy('month', 'DESC')
                        ->take(10)
                        ->get(['month', 'user_id'])
                );
            })
        );

        //Top User View Year
        $top_user_view_year = json_decode(
            Cache::remember('top_user_view_year' , $expiresAt, function () {
                return json_encode(
                    UserView::with(['user'])
                        ->whereNotIn('user_id', [1945])
                        ->whereHas('user', function ($q) {
                            $q->where('active', BaseConstants::ACTIVE);
                            $q->where('type', 1);
                        })
                        ->orderBy('year', 'DESC')
                        ->take(10)
                        ->get(['year', 'user_id'])
                );
            })
        );

        //Top cống hiến
        $top_contributions = json_decode(
            Cache::remember('top_contributions' , $expiresAt, function () {
                $top_contributions = User::join('stories', 'users.id', 'stories.user_id')
                    ->where('users.active', BaseConstants::ACTIVE)
                    ->where('stories.total_view', '>=', 10000)
                    ->whereYear('stories.created_at', '=', date('Y'))
                    ->groupBy('users.id')
                    ->orderBy('total_10k', 'DESC')
                    ->selectRaw('users.*, count(stories.user_id) as total_10k')
                    ->take(10)
                    ->get(['id', 'avatar', 'name']);
                return json_encode(collect($top_contributions)->sortByDesc('total_story_10k_view'));
            })
        );

        $top = [
            'top_day' => $top_day,
            'top_week' => $top_week,
            'top_month' => $top_month,
            'top_year' => $top_year,
            'top_user_view_day' => $top_user_view_day,
            'top_user_view_week' => $top_user_view_week,
            'top_user_view_month' => $top_user_view_month,
            'top_user_view_year' => $top_user_view_year,
            'top_contributions' => $top_contributions
        ];

        $top_day_html = '';
        foreach($top['top_day'] as $key => $top_day_item) {
            $last_chapter = '<div class="last_chapter">
                ' . $top_day_item->story->last_chapter . '
            </div>';
            $top_day_html .= '<div class="d-flex top-item">
                <div class="stt" style="color: ' . $colorArray[$key] . '">' . $key + 1 . '</div>
                <div class="thumbnail">
                    <img src="' . asset('img/ajax-loading.gif') . '" class="lazyload"
                    data-src="' . asset('images/story/thumbs/230/' . $top_day_item->story->thumbnail) . '"
                    alt="' . $top_day_item->story->name . '" onerror="this.src=\'' . asset('img/no-image.png') . '\'">
                </div>
                <div class="detail">
                    <h3>
                        <a href="' . route('story.detail', $top_day_item->story->slug) . '">
                            ' . $top_day_item->story->name . '
                        </a>
                    </h3>
                    <div class="d-flex justify-content-between">
                        ' . $last_chapter . '
                        <div class="view">
                            <i class="bx bx-show"></i> ' . number_format($top_day_item->day) . '
                        </div>
                    </div>
                </div>
            </div>';
        }

        $top_week_html = '';
        foreach($top['top_week'] as $key => $top_week_item) {
            $last_chapter = '<div class="last_chapter">
                ' . $top_week_item->story->last_chapter . '
            </div>';
            $top_week_html .= '<div class="d-flex top-item">
                <div class="stt" style="color: ' . $colorArray[$key] . '">' . $key + 1 . '</div>
                <div class="thumbnail">
                    <img src="' . asset('img/ajax-loading.gif') . '" class="lazyload"
                        data-src="' . asset('images/story/thumbs/230/' . $top_week_item->story->thumbnail) . '"
                        alt="' . $top_week_item->story->name . '" onerror="this.src=\'' . asset('img/no-image.png') . '\'">
                </div>
                <div class="detail">
                    <h3>
                        <a href="' . route('story.detail', $top_week_item->story->slug) . '">
                            ' . $top_week_item->story->name . '
                        </a>
                    </h3>
                    <div class="d-flex justify-content-between">
                        ' . $last_chapter . '
                        <div class="view">
                            <i class="bx bx-show"></i> ' . number_format($top_week_item->week) . '
                        </div>
                    </div>
                </div>
            </div>';
        }

        $top_month_html = '';
        foreach($top['top_month'] as $key => $top_month_item) {
            $last_chapter = '<div class="last_chapter">
                ' . $top_month_item->story->last_chapter . '
            </div>';
            $top_month_html .= '<div class="d-flex top-item">
                <div class="stt" style="color: ' . $colorArray[$key] . '">' . $key + 1 . '</div>
                <div class="thumbnail">
                    <img src="' . asset('img/ajax-loading.gif') . '" class="lazyload"
                        data-src="' . asset('images/story/thumbs/230/' . $top_month_item->story->thumbnail) . '"
                        alt="' . $top_month_item->story->name . '" onerror="this.src=\'' . asset('img/no-image.png') . '\'">
                </div>
                <div class="detail">
                    <h3>
                        <a href="' . route('story.detail', $top_month_item->story->slug) . '">
                            ' . $top_month_item->story->name . '
                        </a>
                    </h3>
                    <div class="d-flex justify-content-between">
                        ' . $last_chapter . '
                        <p class="view">
                            <i class="bx bx-show"></i> ' . number_format($top_month_item->month) . '
                        </p>
                    </div>
                </div>
            </div>';
        }

        $top_year_html = '';
        foreach($top['top_year'] as $key => $top_year_item) {
            $last_chapter = '<div class="last_chapter">
                ' . $top_year_item->story->last_chapter . '
            </div>';
            $top_year_html .= '<div class="d-flex top-item">
                <div class="stt" style="color: ' . $colorArray[$key] . '">' . $key + 1 . '</div>
                <div class="thumbnail">
                    <img src="' . asset('img/ajax-loading.gif') . '" class="lazyload"
                        data-src="' . asset('images/story/thumbs/230/' . $top_year_item->story->thumbnail) . '"
                        alt="' . $top_year_item->story->name . '" onerror="this.src=\'' . asset('img/no-image.png') . '\'">
                </div>
                <div class="detail">
                    <h3>
                        <a href="' . route('story.detail', $top_year_item->story->slug) . '">
                            ' . $top_year_item->story->name . '
                        </a>
                    </h3>
                    <div class="d-flex justify-content-between">
                        ' . $last_chapter . '
                        <p class="view">
                            <i class="bx bx-show"></i> ' . number_format($top_year_item->year) . '
                        </p>
                    </div>
                </div>
            </div>';
        }

        $top_contributions_html = '';
        $i = 0;
        foreach($top['top_contributions'] as $key => $top_contributions_item) {
            $last_chapter = '<div class="last_chapter">
                <i class="bx bx-book-alt"></i> ' . number_format($top_contributions_item->total_10k) . '
            </div>';

            $avatar = asset('img/avata.png');
            if ($top_contributions_item->avatar != '') {
                $avatar = asset('images/avatar/thumbs/230/' . $top_contributions_item->avatar);
            }

            $verified = '';
            if ($top_contributions_item->featured) {
                $verified = '<img src="' . asset('img/icon-medal.png') . '" class="mb-1" width="20" alt="Featured"/>';
            }

            if ($top_contributions_item->super_star) {
                $verified = '<img src="' . asset('img/icon-star.gif') . '" class="mb-1" width="20" alt="Super Star"/>';
            }

            $top_contributions_html .= '<div class="d-flex top-item">
                <div class="stt" style="color: ' . $colorArray[$key] . '">' . $i + 1 . '</div>
                <div class="thumbnail">
                    <img data-src="' . $avatar . '" alt="" src="' . asset('img/ajax-loading.gif') . '"
                    class="border-50 lazyload" onerror="this.src=\'' . asset('img/avata.png') . '\';">
                </div>
                <div class="detail">
                    <h3>
                        <a href="' . route('translateTeam.detail', $top_contributions_item->id) . '">
                            ' . $top_contributions_item->name . '
                            ' . $verified . '
                        </a>
                    </h3>
                    <div class="d-flex justify-content-between">
                        ' . $last_chapter . '
                        <div class="view">
                            TOP ' . $i + 1 . '
                        </div>
                    </div>
                </div>
            </div>';
            $i++;
        }

        $top_user_view_day_html = '';
        foreach($top['top_user_view_day'] as $key => $top_user_view_day_item) {
            $avatar = asset('img/avata.png');
            if ($top_user_view_day_item->user->avatar != '') {
                $avatar = asset('images/avatar/thumbs/230/' . $top_user_view_day_item->user->avatar);
            }

            $verified = '';
            if ($top_user_view_day_item->user->featured) {
                $verified = '<img src="' . asset('img/icon-medal.png') . '" class="mb-1" width="20" alt="Featured"/>';
            }

            if ($top_user_view_day_item->user->super_star) {
                $verified = '<img src="' . asset('img/icon-star.gif') . '" class="mb-1" width="20" alt="Super Star"/>';
            }

            $top_user_view_day_html .= '<div class="d-flex top-item">
                <div class="stt" style="color: ' . $colorArray[$key] . '">' . $key + 1 . '</div>
                <div class="thumbnail">
                    <img data-src="' . $avatar . '" class="border-50 lazyload" src="' . asset('img/ajax-loading.gif') . '"
                    alt="' . $top_user_view_day_item->user->name . '" onerror="this.src=\'' . asset('img/no-image.png') . '\'">
                </div>
                <div class="detail">
                    <h3>
                        <a href="' . route('translateTeam.detail', $top_user_view_day_item->user->id) . '">
                            ' . $top_user_view_day_item->user->name . '
                            ' . $verified . '
                        </a>
                    </h3>
                    <div class="d-flex justify-content-between">
                        <div class="view">
                            <i class="bx bx-show"></i> ' . number_format($top_user_view_day_item->day) . '
                        </div>
                    </div>
                </div>
            </div>';
        }

        $top_user_view_week_html = '';
        foreach($top['top_user_view_week'] as $key => $top_user_view_week_item) {
            $avatar = asset('img/avata.png');
            if ($top_user_view_week_item->user->avatar != '') {
                $avatar = asset('images/avatar/thumbs/230/' . $top_user_view_week_item->user->avatar);
            }

            $verified = '';
            if ($top_user_view_week_item->user->featured) {
                $verified = '<img src="' . asset('img/icon-medal.png') . '" class="mb-1" width="20" alt="Featured"/>';
            }

            if ($top_user_view_week_item->user->super_star) {
                $verified = '<img src="' . asset('img/icon-star.gif') . '" class="mb-1" width="20" alt="Super Star"/>';
            }

            $top_user_view_week_html .= '<div class="d-flex top-item">
                <div class="stt" style="color: ' . $colorArray[$key] . '">' . $key + 1 . '</div>
                <div class="thumbnail">
                    <img data-src="' . $avatar . '" class="border-50 lazyload" src="' . asset('img/ajax-loading.gif') . '"
                    alt="' . $top_user_view_week_item->user->name . '" onerror="this.src=\'' . asset('img/no-image.png') . '\'">
                </div>
                <div class="detail">
                    <h3>
                        <a href="' . route('translateTeam.detail', $top_user_view_week_item->user->id) . '">
                            ' . $top_user_view_week_item->user->name . '
                            ' . $verified . '
                        </a>
                    </h3>
                    <div class="d-flex justify-content-between">
                        <div class="view">
                            <i class="bx bx-show"></i> ' . number_format($top_user_view_week_item->week) . '
                        </div>
                    </div>
                </div>
            </div>';
        }

        $top_user_view_month_html = '';
        foreach($top['top_user_view_month'] as $key => $top_user_view_month_item) {
            $avatar = asset('img/avata.png');
            if ($top_user_view_month_item->user->avatar != '') {
                $avatar = asset('images/avatar/thumbs/230/' . $top_user_view_month_item->user->avatar);
            }

            $verified = '';
            if ($top_user_view_month_item->user->featured) {
                $verified = '<img src="' . asset('img/icon-medal.png') . '" class="mb-1" width="20" alt="Featured"/>';
            }

            if ($top_user_view_month_item->user->super_star) {
                $verified = '<img src="' . asset('img/icon-star.gif') . '" class="mb-1" width="20" alt="Super Star"/>';
            }

            $top_user_view_month_html .= '<div class="d-flex top-item">
                <div class="stt" style="color: ' . $colorArray[$key] . '">' . $key + 1 . '</div>
                <div class="thumbnail">
                    <img data-src="' . $avatar . '" class="border-50 lazyload" src="' . asset('img/ajax-loading.gif') . '"
                    alt="' . $top_user_view_month_item->user->name . '" onerror="this.src=\'' . asset('img/no-image.png') . '\'">
                </div>
                <div class="detail">
                    <h3>
                        <a href="' . route('translateTeam.detail', $top_user_view_month_item->user->id) . '">
                            ' . $top_user_view_month_item->user->name . '
                            ' . $verified . '
                        </a>
                    </h3>
                    <div class="d-flex justify-content-between">
                        <div class="view">
                            <i class="bx bx-show"></i> ' . number_format($top_user_view_month_item->month) . '
                        </div>
                    </div>
                </div>
            </div>';
        }

        $top_user_view_year_html = '';
        foreach($top['top_user_view_year'] as $key => $top_user_view_year) {
            $avatar = asset('img/avata.png');
            if ($top_user_view_year->user->avatar != '') {
                $avatar = asset('images/avatar/thumbs/230/' . $top_user_view_year->user->avatar);
            }

            $verified = '';
            if ($top_user_view_year->user->featured) {
                $verified = '<img src="' . asset('img/icon-medal.png') . '" class="mb-1" width="20" alt="Featured"/>';
            }

            if ($top_user_view_year->user->super_star) {
                $verified = '<img src="' . asset('img/icon-star.gif') . '" class="mb-1" width="20" alt="Super Star"/>';
            }

            $top_user_view_year_html .= '<div class="d-flex top-item">
                <div class="stt" style="color: ' . $colorArray[$key] . '">' . $key + 1 . '</div>
                <div class="thumbnail">
                    <img data-src="' . $avatar . '" class="border-50 lazyload" src="' . asset('img/ajax-loading.gif') . '"
                    alt="' . $top_user_view_year->user->name . '" onerror="this.src=\'' . asset('img/no-image.png') . '\'">
                </div>
                <div class="detail">
                    <h3>
                        <a href="' . route('translateTeam.detail', $top_user_view_year->user->id) . '">
                            ' . $top_user_view_year->user->name . '
                            ' . $verified . '
                        </a>
                    </h3>
                    <div class="d-flex justify-content-between">
                        <div class="view">
                            <i class="bx bx-show"></i> ' . number_format($top_user_view_year->year) . '
                        </div>
                    </div>
                </div>
            </div>';
        }

        $html .= '<div class="row">
                <div class="col-md-12">
                    <div class="card widget-top">
                        <div class="card-body p-2">
                            <h5 class="text-center text-uppercase mb-3 mt-2" style="font-weight: bold">Bảng xếp hạng</h5>
                            <ul class="nav nav-pills mb-3 justify-content-center" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="pill" href="#top-day" role="tab" aria-selected="true">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">Ngày</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="pill" href="#top-week" role="tab" aria-selected="false" tabindex="-1">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">Tuần</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="pill" href="#top-month" role="tab" aria-selected="false" tabindex="-1">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">Tháng</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="pill" href="#top-year" role="tab" aria-selected="false" tabindex="-1">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">Năm</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="top-day" role="tabpanel">' . $top_day_html . '</div>
                                <div class="tab-pane fade" id="top-week" role="tabpanel">' . $top_week_html . '</div>
                                <div class="tab-pane fade" id="top-month" role="tabpanel">' . $top_month_html . '</div>
                                <div class="tab-pane fade" id="top-year" role="tabpanel">' . $top_year_html . '</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card widget-top top-user-view">
                        <div class="card-body p-2">
                            <h5 class="text-center text-uppercase mb-3 mt-2" style="font-weight: bold">Top View</h5>
                            <ul class="nav nav-pills mb-3 justify-content-center" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="pill" href="#top-user-view-day" role="tab" aria-selected="true">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">Ngày</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="pill" href="#top-user-view-week" role="tab" aria-selected="false" tabindex="-1">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">Tuần</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="pill" href="#top-user-view-month" role="tab" aria-selected="false" tabindex="-1">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">Tháng</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="pill" href="#top-user-view-year" role="tab" aria-selected="false" tabindex="-1">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">Năm</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="top-user-view-day" role="tabpanel">' . $top_user_view_day_html . '</div>
                                <div class="tab-pane fade" id="top-user-view-week" role="tabpanel">' . $top_user_view_week_html . '</div>
                                <div class="tab-pane fade" id="top-user-view-month" role="tabpanel">' . $top_user_view_month_html . '</div>
                                <div class="tab-pane fade" id="top-user-view-year" role="tabpanel">' . $top_user_view_year_html . '</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card widget-top top-user-contributions">
                        <div class="card-body p-2">
                            <h5 class="text-center text-uppercase mb-3 mt-2" style="font-weight: bold">Vinh Danh</h5>
                            <ul class="nav nav-pills nav-pills-danger mb-3 justify-content-center" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="pill" href="#top-contributions" role="tab" aria-selected="true">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">Cống hiến</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="danger-pills-tabContent">
                                <div class="tab-pane fade show active" id="top-contributions" role="tabpanel">' . $top_contributions_html . '</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        return $html;
    }

    public function RelatedStoryRender($relatedStories)
    {
        $html = '';
        $sectionTitle = '<h5 class="mb-0 text-uppercase">Truyện gợi ý</h5><hr>';
        $blockStory = '';
        foreach ($relatedStories as $item) {
            $link = route('story.detail', [$item->slug]);

            $blockStory .= '<div class="single-story-block">
                <div class="single-story-wrap">
                    <div class="single-story-img">
                        <a href="' . $link . '">
                            <img
                                src="' . asset('images/story/thumbs/230/' . $item->thumbnail) . '"
                                alt="' . $item->name . '"
                                onerror="this.src=\'' . asset('img/no-image.png') . '\'"
                            />
                        </a>
                    </div>
                </div>
                <div class="single-story-details">
                    <h3 class="story-item-title"><a href="' . $link . '">' . $item->name . '</a></h3>
                </div>
            </div>';
        }
        $html .= '<div class="top-ten-comics-area pb-3">
            <div class="row">
                <div class="col-lg-12">
                    <div class="slider-recommend-right background-primary">
                        <div class="total-item-show">
                            ' . $sectionTitle . '
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="top-story-slider owl-carousel" id="completed-comics-slider">
                                    ' . $blockStory . '
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        return $html;
    }

    public function SliderHotStories()
    {
        $html = '';
        if (Cache::has('slider_hot_story')) {
            $data = Cache::get('slider_hot_story');
            $data = json_decode($data);
        } else {
            $data = Story::with(['user'])
                ->where('status', BaseConstants::ACTIVE)
                ->where('proposed', BaseConstants::ACTIVE)
                ->orderBy('updated_at', 'DESC')
                ->take(15)
                ->get(
                    [
                        'id',
                        'user_id',
                        'name',
                        'slug',
                        'content',
                        'categories',
                        'cover_image',
                        'total_view',
                        'total_bookmark',
                        'last_chapter',
                        'is_full',
                        'hot',
                        'created_at',
                        'updated_at',
                    ]
                );

            foreach ($data as $item) {
                $categories = Category::whereIn('id', json_decode($item->categories))
                    ->get();
                $item->story_categories = $categories;
            }

            $expiresAt = Carbon::now()->addMinutes(15);
            Cache::put('slider_hot_story', json_encode($data), $expiresAt);
        }

        if (count($data)) {
            $html .= '<div class="hot-stories-slider owl-carousel">';
        }
        foreach ($data as $item) {
            $link = route('story.detail', $item->slug);
            $cover = asset($item->cover_image);
            $spanFull = '';
            if ($item->is_full) {
                $spanFull = '<div class="badge-custom">FULL</div>';
            }
            $blockCategory = '';
            foreach ($item->story_categories as $cateItem) {
                if ($blockCategory == '') {
                    $blockCategory .= '<a href="' . route('category.list', $cateItem->slug) . '">' . $cateItem->name . '</a>';
                } else {
                    $blockCategory .= ', <a href="' . route('category.list', $cateItem->slug) . '">' . $cateItem->name . '</a>';
                }
            }

            $content = strip_tags(htmlspecialchars_decode($item->content), '<p>');
            $html .= '<div class="hot-slider-item position-relative">
                <div class="slider-overlay"><a href="' . $link . '"></a></div>
                <div class="cover-image">
                    <img src="' . $cover . '" alt="' . $item->name . '" width="1920" height="600">
                </div>
                <div class="story-info">
                    <div class="container">
                        <h3 class="d-flex justify-content-start align-items-center">' . $spanFull . '<a href="' . $link . '">' . $item->name . '</a></h3>
                        <div class="block-slider-item">
                            <b>Thể loại: </b>' . $blockCategory . '
                        </div>
                        <div class="block-slider-item">
                            <b>Lượt xem: </b>' . number_format($item->total_view) . '
                        </div>
                        <div class="description">
                            ' . self::excerpts($content, 200) . '
                        </div>
                    </div>
                </div>
            </div>';
        }
        $html .= '</div>';
        return $html;
    }

    public function ProposeStory()
    {
        $html = '';
        $sectionTitle = '<h5 class="mb-0 text-uppercase">Truyện Hot Tháng Này</h5> <hr>';
        $stories = Cache::remember('ProposeStory' , Carbon::now()->addMinutes(15), function () {
            return json_encode(
                Story::orderBy('total_view', 'DESC')
                    ->whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'))
                    ->where('status', BaseConstants::ACTIVE)
                    ->whereHas('user', function ($q) {
                        $q->where('active', BaseConstants::ACTIVE);
                        $q->where('type', 1);
                    })
                    ->take(20)
                    ->get([
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
                        'type',
                        'created_at',
                        'updated_at',
                    ])
            );
        });
        $stories = json_decode($stories);

        if (count($stories) > 10) {
            $blockStory = '';
            foreach ($stories as $item) {
                $link = route('story.detail', [$item->slug]);

                $rating = '';
                if ($item->rating > 0) {
                    $rating = '<span>⭐ ' . number_format($item->rating) . '</span>';
                }

                $blockStory .= '<div class="single-story-block">
                    <div class="single-story-wrap">
                        <div class="single-story-img position-relative">
                            <a href="' . $link . '">
                                <img
                                    class="lazyload" src="' . asset('img/ajax-loading.gif') . '"
                                    data-src="' . asset('images/story/thumbs/230/' . $item->thumbnail) . '"
                                    alt="' . $item->name . '" width="200" height="260"
                                    onerror="this.src=\'' . asset('img/no-image.png') . '\'"
                                />
                            </a>
                            <div class="story-meta-data d-flex justify-content-start">
                                <span><i class="bx bx-show"></i> ' . number_format($item->total_view) . '</span>
                                <span><i class="bx bx-bookmark-alt"></i> ' . number_format($item->total_bookmark) . '</span>
                                ' . $rating . '
                            </div>
                        </div>
                    </div>
                    <div class="single-story-details">
                        <h3><a href="' . $link . '">' . $item->name . '</a></h3>
                    </div>
                </div>';
            }
            $html .= '<div class="top-ten-stories-area">
                <div class="slider-recommend-right background-primary">
                    <div class="total-item-show position-relative">
                        ' . $sectionTitle . '
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="top-story-slider owl-carousel" id="propose-story-slider">
                                ' . $blockStory . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }
        return $html;
    }

    public function renderMenuMobile()
    {
        if (Cache::has('menu_mobile')) {
            $html = Cache::get('menu_mobile');
            $html = htmlspecialchars_decode($html);
        } else {
            $html = '<li class="nav-item nav-expand">
            <a class="nav-link nav-expand-link" href="javascript:void(0)">
                <span class="menu-item-title">Thể Loại</span>
                <span class="icon-expand"><i class="fas fa-chevron-right"></i></span>
            </a>
            <ul class="nav-items nav-expand-content">';
            if (Cache::has('category_menu')) {
                $list_category = Cache::get('category_menu');
            } else {
                $list_category = Comics_Category::where('parent', 0)
                    ->orderby('name', 'ASC')
                    ->get(['name', 'slug', 'id']);
                $expiresAt = Carbon::now()->addMinutes(60);
                Cache::put('category_menu', $list_category, $expiresAt);
            }
            if($list_category) {
                foreach($list_category as $item_menu_mobile) {
                    $item_submenu = Comics_Category::where('parent', $item_menu_mobile->id)
                        ->orderby('name', 'ASC')
                        ->get(['name', 'slug', 'id']);
                    $cls_menu_nav = "";
                    $cls_menu_sub = "";
                    if (count($item_submenu) > 0) {
                        $cls_menu_nav = "nav-expand";
                        $cls_menu_sub = "nav-expand-link clear";
                    }

                    $html .= '<li class="nav-item ' . $cls_menu_nav . '">
                <a class="nav-link ' . $cls_menu_sub . '" href="' . route('story.detail', array($item_menu_mobile->slug)) . '">
                <span class="menu-item-title">' . $item_menu_mobile->name . '</span>';
                    if( count($item_submenu) > 0 ) {
                        $html .= '<span class="icon-expand"><i class="fas fa-chevron-right"></i></span>';
                    }
                    $html .= '</a>';
                    if( count($item_submenu) > 0 ) {
                        $html .= '<ul class="nav-items nav-expand-content">';
                        foreach($item_submenu as $item_menu_mobile_child) {
                            $item_submenu_child = Comics_Category::where(
                                'parent',
                                '=',
                                $item_menu_mobile_child->id
                            )
                                ->orderby('name', 'ASC')
                                ->get(['name', 'slug', 'id']);
                            $cls_menu_nav_child = "";
                            $cls_menu_sub_child = "";
                            if (count($item_submenu_child) > 0) {
                                $cls_menu_nav_child = "nav-expand";
                                $cls_menu_sub_child = "nav-expand-link clear";
                            }
                            $html .= '<li class="nav-item ' . $cls_menu_nav_child . '">
                        <a class="nav-link ' . $cls_menu_sub_child . '" href="' . route('story.detail', array($item_menu_mobile_child->slug)) . '">
                        <span class="menu-item-title">' . $item_menu_mobile_child->name . '</span>';
                            if( count($item_submenu_child) > 0 ) {
                                $html .= '<span class="icon-expand"><i class="fas fa-chevron-right"></i></span>';
                            }
                            $html .= '</a>';
                            if( count($item_submenu_child) > 0 ) {
                                $html .= '<ul class="nav-items nav-expand-content">';
                                foreach($item_submenu_child as $item_menu_mobile_child_lv2) {
                                    $cls_menu_nav_child_lv2 = "";
                                    $cls_menu_sub_child_lv2 = "";
                                    $html .= '<li class="nav-item ' . $cls_menu_nav_child_lv2 . '">
                                <a class="nav-link ' . $cls_menu_sub_child_lv2 . '"
                                   href="' . route('story.detail', array($item_menu_mobile_child_lv2->slug)) . '">
                                    <span class="menu-item-title">' . $item_menu_mobile_child_lv2->name . '</span>
                                </a>
                            </li>';
                                }
                                $html .= '</ul>';
                            }
                        }
                        $html .= '</ul>';
                    }
                    $html .= '</li>';
                }
            }
            $html .= '</ul></li>';
            $expiresAt = Carbon::now()->addMinutes(120);
            Cache::put('menu_mobile', htmlspecialchars($html), $expiresAt);
        }
        return $html;
    }

    public static function formatMoney12($number, $fractional = false)
    {
        if ($fractional) {
            $number = sprintf('%.2f', $number);
        }
        while (true) {
            $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
            if ($replaced != $number) {
                $number = $replaced;
            } else {
                break;
            }
        }
        return $number;
    }

    public static function format_price($price)
    {
        return number_format($price, 0, ',', '.');
    }

    function objectToArray($o)
    {
        $a = array();
        foreach ($o as $k => $v) {
            $a[$k] = (is_array($v) || is_object($v)) ? objectToArray($v) : $v;
        }
        return $a;
    }

    public static function time_request($time)
    {
        $date_current = date('Y-m-d H:i:s');
        $s = strtotime($date_current) - strtotime($time);
        if ($s <= 60) { // if < 60 seconds
            return '1 phút trước';
        } else {
            $t = intval($s / 60);
            if ($t >= 60) {
                $t = intval($t / 60);
                if ($t >= 24) {
                    $t = intval($t / 24);
                    if ($t >= 30) {
                        $t = intval($t / 30);
                        if ($t >= 12) {
                            $t = intval($t / 12);
                            return $t . ' năm trước';
                        } else {
                            return $t . ' tháng trước';
                        }
                    } else {
                        return $t . ' ngày trước';
                    }
                } else {
                    return $t . ' giờ trước';
                }
            } else {
                return $t . ' phút trước';
            }
        }
    }

    public
    function get_template_page(
        $slug
    ) {
        $content = "";
        $page = Page::where('slug', $slug)
            ->where('status', 1)
            ->first();
        if (isset($page) && $page):
            $content = htmlspecialchars_decode($page->content);
        else:
            $content = "";
        endif;
        return $content;
    }

    public
    static function objectEmpty(
        $o
    ) {
        if (empty($o)) {
            return true;
        } else {
            if (is_numeric($o)) {
                return false;
            } else {
                if (is_string($o)) {
                    return !strlen(trim($o));
                } else {
                    if (is_object($o)) {
                        return self::objectEmpty((array)$o);
                    }
                }
            }
        }
        // It's an array!
        foreach ($o as $element) {
            if (self::objectEmpty($element)) {
                continue;
            } // so far so good.
            else {
                return false;
            }
        }

        // all good.
        return true;
    }

    public
    function excerpts(
        $str,
        $length = 200,
        $trailing = '..'
    ) {
        $str = str_replace("  ", " ", $str);
        if (!empty($str)):
            $str = strip_tags($str);
        endif;
        $str = strip_tags($str);
        $length -= mb_strlen($trailing);
        if (mb_strlen($str) > $length):
            return mb_strimwidth($str, 0, $length, $trailing, 'utf-8');
        else:
            $str = str_replace("  ", " ", $str);
            $res = $str;
        endif;
        return $res;
    }
}//end class
