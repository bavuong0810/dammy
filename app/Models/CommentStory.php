<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentStory extends Model
{
    protected $table = 'comments';
    protected $fillable =[
        'id',
        'user_id',
        'story_id',
        'chapter_id',
        'content',
        'parent',
        'created_at',
        'updated_at'
    ];

    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id', 'id');
    }
}
