<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryReview extends Model
{
    protected $table = 'story_reviews';
    protected $fillable =[
        'id',
        'user_id',
        'story_id',
        'rating',
        'content',
        'created_at',
        'updated_at',
        'status',
        'total_comment',
        'like_count'
    ];

    const Status = [
        'Chờ duyệt' => 0,
        'Duyệt' => 1,
        'Không duyệt' => 2
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(StoryReviewComment::class, 'review_id', 'id');
    }
}
