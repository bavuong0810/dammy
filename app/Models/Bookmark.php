<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    public $timestamps = false;
    protected $table = 'bookmarks';
    protected $fillable =[
        'id',
        'story_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function stories()
    {
        return $this->belongsTo(Story::class, 'story_id', 'id');
    }
}
