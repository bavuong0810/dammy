<?php

namespace App\Http\Controllers;

use App\Constants\BaseConstants;
use App\Libraries\Helpers;
use App\Models\Story;
use App\Models\StoryReview;
use App\Models\StoryReviewComment;
use App\Tasks\TelegramTask;
use App\WebService\WebService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    private $telegramTask;
    public function __construct(){
        $this->telegramTask = new TelegramTask();
    }

    public function getReviews(Request $request)
    {
        $story_id = $request->story_id;
        return WebService::ReviewRender($story_id);
    }

    public function getReviewComments(Request $request)
    {
        $review_id = $request->review_id;
        return WebService::ReviewCommentRender($review_id);
    }

    public function loadMoreReview(Request $request)
    {
        $pageNumber = $request->page;
        $story_id = $request->story_id;
        return response()->json(
            [
                'success' => true,
                'html' => WebService::ReviewRender($story_id, $pageNumber)
            ]
        );
    }

    public function sentReview(Request $request)
    {
        $user_id = Auth::user()->id;
        $ratingOnly = strip_tags($request->ratingOnly);
        $reviewRating = strip_tags($request->reviewRating);
        $reviewContent = strip_tags($request->reviewContent);
        if ($ratingOnly) {
            $reviewContent = '';
        }
        $story_id = (int)$request->story_id;

        $story = Story::find($story_id);
        if ($story) {
            if ($user_id == $story->user_id) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Bạn không thể tự đánh giá truyện của mình.'
                    ]
                );
            }

            //check user đã review hay chưa
            $checkReview = StoryReview::where('user_id', $user_id)
                ->where('story_id', $story_id)
                ->first();
            if (!$checkReview) {
                $review = StoryReview::create(
                    [
                        'user_id' => $user_id,
                        'story_id' => $story_id,
                        'rating' => (int)$reviewRating,
                        'content' => $reviewContent,
                        'status' => StoryReview::Status['Chờ duyệt'],
                        'total_comment' => 0,
                        'like_count' => 0
                    ]
                );

                if ($review) {
                    if (env('APP_ENV') == 'production') {
                        $adminLink = route('admin.review.index');
                        $storyLink = $request->currentLink;
                        $text = <<<EOT
                        **[Đam Mỹ] Đánh giá mới**
                        **Link truyện:** $storyLink
                        **Rating:** $reviewRating ⭐
                        **Nội dung:** $reviewContent
                        **Admin Link:** $adminLink
                        EOT;
                        $this->telegramTask->sendMessage($text, 'review');
                    }

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Cảm ơn bạn đã đánh giá truyện. Đánh giá của bạn sẽ được Đam Mỹ team kiểm duyệt đọc trước khi được hiển thị.'
                        ]
                    );
                }
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Bạn đã tham gia đánh giá truyện này. Nếu đánh giá của bạn vẫn chưa được hiển thị, vui lòng chờ Đam Mỹ kiểm duyệt đánh giá của bạn.'
                    ]
                );
            }
        }

        return response()->json(
            [
                'success' => false,
                'message' => 'Đã xãy ra lỗi trong quá trình gửi đánh giá.'
            ]
        );
    }

    public function sentReplyReview(Request $request)
    {
        $user_id = Auth::user()->id;
        $review_id = (int)$request->review_id;
        $content = strip_tags($request->replyContent);
        $story_id = (int)$request->story_id;

        $review = StoryReview::find($review_id);
        if ($review) {
            if ($content != '') {
                $comment = StoryReviewComment::create(
                    [
                        'user_id' => $user_id,
                        'story_id' => $story_id,
                        'review_id' => $review_id,
                        'content' => $content,
                        'status' => BaseConstants::ACTIVE,
                        'like_count' => 0
                    ]
                );
                if ($comment) {
                    $comments[] = $comment;
                    $html = view('partials.block-review-comment', compact('comments', 'review_id'))->render();

                    $totalComment = StoryReviewComment::where('review_id', $review_id)
                        ->where('status', BaseConstants::ACTIVE)
                        ->count();
                    $review->total_comment = $totalComment;
                    $review->save();

                    Cache::forget('reviews_story_1_id_' . $story_id);
                    Cache::forget('reviews_story_2_id_' . $story_id);
                    Cache::forget('reviews_comments_id_' . $review_id);
                    return response()->json(
                        [
                            'success' => true,
                            'html' => $html
                        ]
                    );
                } else {
                    return response()->json(['success' => false, 'message' => 'Đã xãy ra lỗi trong quá trình gửi trả lời. Vui lòng thử lại sau!']);
                }
            }
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập nội dung trả lời!']);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy đánh giá này!']);
    }
}
