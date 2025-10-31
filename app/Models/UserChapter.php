<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserChapter extends Model
{
    public $timestamps = false;
    protected $table = 'user_chapters';
    protected $fillable =[
        'id',
        'story_id',
        'chapter_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id', 'id');
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id', 'id');
    }
}
