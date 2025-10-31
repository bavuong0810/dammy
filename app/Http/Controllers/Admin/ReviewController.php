<?php

namespace App\Http\Controllers\Admin;

use App\Constants\BaseConstants;
use App\Models\Story;
use App\Models\StoryReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class ReviewController extends Controller
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

    public function index(Request $request)
    {
        $query = StoryReview::with(
            [
                'user' => function ($q) {
                    $q->select('id', 'name');
                },
                'story' => function ($q) {
                    $q->select('id', 'name', 'slug');
                }
            ]
        )
            ->orderBy('status', 'ASC')
            ->orderBy('created_at', 'DESC');

        $reviews = $query->paginate(50);
        return view('admin.review.index', compact('reviews'));
    }

    public function detail($id)
    {
        $detail = StoryReview::with(
            [
                'user' => function ($q) {
                    $q->select('id', 'name');
                },
                'story' => function ($q) {
                    $q->select('id', 'name', 'slug');
                },
                'comments'
            ]
        )
            ->where('id', $id)->first();
        if ($detail) {
            return view('admin.review.detail', compact('detail'));
        } else {
            return redirect()->route('admin.review.index');
        }
    }

    public function quickAction(Request $request)
    {
        $review_id = $request->review_id;
        $review = StoryReview::find($review_id);
        if ($review) {
            $story_id = $review->story_id;

            $status = (int)$request->status;
            StoryReview::where('id', $review_id)->update(['status' => $status]);

            $sumRating = StoryReview::where('story_id', $story_id)
                ->where('status', BaseConstants::ACTIVE)
                ->sum('rating');
            $totalRating = StoryReview::where('story_id', $story_id)
                ->where('status', BaseConstants::ACTIVE)
                ->count();

            $storyRate = 0;
            if ($totalRating > 0) {
                $cal = $sumRating / $totalRating;
                $storyRate = number_format($cal, 1);
            }

            Story::where('id', $story_id)
                ->update(['total_review' => $totalRating, 'rating' => $storyRate]);

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy đánh giá này!']);
    }
}
