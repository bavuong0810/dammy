<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    public $timestamps = false;
    protected $table = 'stories';
    protected $fillable =[
        'id',
        'user_id',
        'name',
        'slug',
        'search_name',
        'another_name',
        'author',
        'categories',
        'thumbnail',
        'cover_image',
        'content',
        'total_like',
        'total_view',
        'total_listen',
        'total_review',
        'total_bookmark',
        'total_favourite',
        'rating',
        'last_chapter',
        'is_full',
        'audio',
        'type',
        'proposed',
        'hot',
        'status',
        'total_chapter',
        'warning',
        'creative',
        'created_at',
        'updated_at',
        'convert_process'
    ];

    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'story_id', 'id');
    }

    public function comment_stories()
    {
        return $this->hasMany(CommentStory::class);
    }

    public function rating_story()
    {
        return $this->hasMany(RatingStory::class);
    }

    public function views()
    {
        return $this->hasOne(StoryView::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
